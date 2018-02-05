<?php

namespace Netgen\Bundle\EzFormsBundle\Controller;

use Exception;
use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct;
use eZ\Publish\API\Repository\ContentService;
use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\UserService;
use Netgen\Bundle\EzFormsBundle\Form\Type\CreateContentType;
use Netgen\Bundle\EzFormsBundle\Form\Type\CreateUserType;
use Netgen\Bundle\EzFormsBundle\Form\Type\InformationCollectionType;
use Netgen\Bundle\EzFormsBundle\Form\Type\UpdateContentType;
use Netgen\Bundle\EzFormsBundle\Form\Type\UpdateUserType;
use RuntimeException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class DemoController extends Controller
{
    public function demoCreateContentAction(Request $request)
    {
        /** @var Repository $repository */
        $repository = $this->getRepository();
        /** @var ContentService $contentService */
        $contentService = $repository->getContentService();
        /** @var LocationService $locationService */
        $locationService = $repository->getLocationService();
        // @todo for demo purpose, user should have necessary permissions by itself
        $repository->setCurrentUser(
            $repository->getUserService()->loadUserByLogin('admin')
        );
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
                // @todo do something else if needed
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
    }

    public function demoUpdateContentAction(Request $request)
    {
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
    }

    public function demoCreateUserAction(Request $request)
    {
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
                        if ($fieldDefinition->fieldTypeIdentifier === 'ezuser') {
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
    }

    public function demoUpdateUserAction(Request $request)
    {
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
    }

    public function demoInformationCollectionAction(Request $request)
    {
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();
        // @todo for demo purpose, user should have necessary permissions by itself
        $repository->setCurrentUser(
            $repository->getUserService()->loadUserByLogin('admin')
        );

        $content = $contentService->loadContent(126);
        $contentTypeId = $content->versionInfo->contentInfo->contentTypeId;
        $contentType = $repository->getContentTypeService()->loadContentType($contentTypeId);

        $informationCollection = new InformationCollectionStruct();

        $data = new DataWrapper($informationCollection, $contentType);

        // No method to create named builder in framework controller
        /** @var $formBuilder \Symfony\Component\Form\FormBuilderInterface */
        $formBuilder = $this->container->get('form.factory')->createBuilder(InformationCollectionType::class, $data);
        // Adding controls as EzFormsBundle does not do that by itself
        $formBuilder->add('save', SubmitType::class, array('label' => 'Publish'));

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var InformationCollectionStruct $data */
            $data = $form->getData()->payload;
            // save data to database
            // or something else
            // this is left for end developer
        }

        return $this->render(
            'NetgenEzFormsBundle::demo_form.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }
}
