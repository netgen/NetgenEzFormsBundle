Documentation
=============

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

```

## User creation

```php
```

## User updating

```php

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
