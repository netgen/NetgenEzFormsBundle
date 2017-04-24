Documentation
=============

For more details about extending please check [this](EXTEND.md).

Currently implemented forms:

| Entity / Form             | Create | Update | Information collection
| ------------- | --- | --- | ---
| Content      | yes | yes | yes
| User      | yes | yes | no

## Content creation

```php
/** @var Repository $repository */
$repository = $this->getRepository();
/** @var ContentService $contentService */
$contentService = $repository->getContentService();
/** @var LocationService $locationService */
$locationService = $repository->getLocationService();

$contentType = $repository->getContentTypeService()
    ->loadContentTypeByIdentifier('test_type');
$contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-GB');

$data = new DataWrapper($contentCreateStruct, $contentCreateStruct->contentType);

/** @var $formBuilder \Symfony\Component\Form\FormBuilderInterface */
$formBuilder = $this->container->get('form.factory')
    ->createBuilder(CreateContentType::class, $data);

$formBuilder->add('save', SubmitType::class, array('label' => 'Publish'));

$form = $formBuilder->getForm();
$form->handleRequest($request);

if ($form->isValid()) {
    $rootLocation = $locationService->loadLocation(2);

    try {
        $repository->beginTransaction();

        $contentDraft = $contentService->createContent(
            $data->payload,
            array(
                $locationService->newLocationCreateStruct($rootLocation->id),
            )
        );

        $content = $contentService->publishVersion($contentDraft->versionInfo);

        $repository->commit();
    } catch (Exception $e) {
        $repository->rollback();
        
        throw $e;
    }

    return $this->redirect(
        $this->generateUrl(
            $locationService->loadLocation(
                $content->contentInfo->mainLocationId
            )
        )
    );
}

return $this->render(
    'AcmeBundle::demo_create_content.html.twig',
    array(
        'form' => $form->createView(),
    )
);
```
## Content updating

```php
/** @var Repository $repository */
$repository = $this->getRepository();
/** @var ContentService $contentService */
$contentService = $repository->getContentService();
$content = $contentService->loadContent(137);
$contentType = $repository->getContentTypeService()
    ->loadContentType($content->contentInfo->contentTypeId);
$contentUpdateStruct = $contentService->newContentUpdateStruct();
$contentUpdateStruct->initialLanguageCode = 'eng-GB';

$data = new DataWrapper($contentUpdateStruct, $contentType, $content);

/** @var $formBuilder \Symfony\Component\Form\FormBuilderInterface */
$formBuilder = $this->container->get('form.factory')
    ->createBuilder(UpdateContentType::class, $data);

$formBuilder->add('save', SubmitType::class, array('label' => 'Update'));

$form = $formBuilder->getForm();
$form->handleRequest($request);

if ($form->isValid()) {
    try {
        $repository->beginTransaction();

        $contentDraft = $contentService->createContentDraft($content->contentInfo);
        $contentDraft = $contentService->updateContent(
            $contentDraft->versionInfo,
            $data->payload
        );
        $content = $contentService->publishVersion($contentDraft->versionInfo);

        $repository->commit();
    } catch (Exception $e) {
        $repository->rollback();

        throw $e;
    }

    return $this->redirect(
        $this->generateUrl(
            $this->getRepository()->getLocationService()->loadLocation(
                $content->contentInfo->mainLocationId
            )
        )
    );
}

return $this->render(
    'AcmeBundle::demo_update_content.html.twig',
    array(
        'form' => $form->createView(),
    )
);
```

## User creation

```php
/** @var Repository $repository */
$repository = $this->getRepository();
/** @var UserService $userService */
$userService = $repository->getUserService();

$contentType = $repository->getContentTypeService()
    ->loadContentTypeByIdentifier('user');
$userCreateStruct = $userService->newUserCreateStruct(
    null,
    null,
    null,
    'eng-GB',
    $contentType
);

$userCreateStruct->enabled = false;

$data = new DataWrapper($userCreateStruct, $userCreateStruct->contentType);

/** @var $formBuilder \Symfony\Component\Form\FormBuilderInterface */
$formBuilder = $this->container->get('form.factory')->createBuilder(CreateUserType::class, $data);

$formBuilder->add('save', SubmitType::class, array('label' => 'Publish'));

$form = $formBuilder->getForm();
$form->handleRequest($request);

if ($form->isValid()) {

    $userGroup = $userService->loadUserGroup(13);

    try {
        $user = $userService->createUser(
            $data->payload,
            array($userGroup)
        );

        // @todo send confirmation email and redirect to proper location (enter confirmation code or something)

        return $this->redirect(
            $this->generateUrl(
                $this->getRepository()->getLocationService()->loadLocation(
                    $user->contentInfo->mainLocationId
                )
            )
        );
    } catch (InvalidArgumentException $e) {
        // There is no better way to do this ATM...
        $existingUsernameMessage = "Argument 'userCreateStruct' is invalid: User with provided login already exists";
        if ($e->getMessage() === $existingUsernameMessage) {
            // Search for the first ezuser field type in content type
            foreach ($userCreateStruct->contentType->getFieldDefinitions() as $fieldDefinition) {
                if ($fieldDefinition->fieldTypeIdentifier == 'ezuser') {
                    $userFieldDefinition = $fieldDefinition;
                    break;
                }
            }

            // UserService validates for this, but it happens AFTER existing username validation
            if (!isset($userFieldDefinition)) {
                throw new RuntimeException("Could not find 'ezuser' field.");
            }

            $form->get($userFieldDefinition->identifier)->addError(
                new FormError('User with provided username already exists.')
            );
        } else {
            // @todo do something else if needed
            throw $e;
        }
    }
}

return $this->render(
    'AcmeBundle::demo_create_user.html.twig',
    array(
        'form' => $form->createView(),
    )
);
```

## User updating

```php
/** @var Repository $repository */
$repository = $this->getRepository();
/** @var UserService $userService */
$userService = $repository->getUserService();
/** @var ContentService $contentService */
$contentService = $repository->getContentService();

$user = $userService->loadUser(142);
$contentType = $repository->getContentTypeService()->loadContentTypeByIdentifier('user');
$contentUpdateStruct = $contentService->newContentUpdateStruct();
$contentUpdateStruct->initialLanguageCode = 'eng-GB';
$userUpdateStruct = $userService->newUserUpdateStruct();
$userUpdateStruct->contentUpdateStruct = $contentUpdateStruct;

$data = new DataWrapper($userUpdateStruct, $contentType, $user);

/** @var $formBuilder \Symfony\Component\Form\FormBuilderInterface */
$formBuilder = $this->container->get('form.factory')->createBuilder(UpdateUserType::class, $data);

$formBuilder->add('save', SubmitType::class, array('label' => 'Update'));

$form = $formBuilder->getForm();
$form->handleRequest($request);

if ($form->isValid()) {
    $user = $userService->updateUser($user, $userUpdateStruct);

    return $this->redirect(
        $this->generateUrl(
            $this->getRepository()->getLocationService()->loadLocation(
                $user->contentInfo->mainLocationId
            )
        )
    );
}

return $this->render(
    'AcmeBundle::demo_update_user.html.twig',
    array(
        'form' => $form->createView(),
    )
);
```

## Supported field types

Currently supported FieldTypes:

| FieldType             | Supported
| ------------- | ---
| Author         | no
| BinaryFile     | yes
| Checkbox       | yes
| Country        | yes
| Date           | yes
| DateAndTime    | yes
| EmailAddress   | yes
| Float          | yes
| Integer        | yes
| Image          | yes
| ISBN           | yes
| Keyword        | no
| Media          | no
| MapLocation    | yes
| Page           | no
| Rating         | no
| Relation       | yes
| RelationList   | no
| RichText       | no
| Selection      | yes
| TextBlock      | yes
| TextLine       | yes
| Time           | yes
| Url            | yes
| User           | yes
| XmlText        | no

## Additional info

Please check [Petar's blog post](http://www.netgenlabs.com/Blog/Creating-and-updating-eZ-Publish-Content-via-Symfony-s-Form-component).
