<?php

namespace Netgen\Bundle\EzFormsBundle\Controller;

use Exception;
use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct;
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
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();
        $locationService = $repository->getLocationService();
        // @todo for demo purpose, user should have necessary permissions by itself
        $repository->setCurrentUser(
            $repository->getUserService()->loadUserByLogin('admin')
        );
        $contentType = $repository->getContentTypeService()->loadContentTypeByIdentifier('test_type');
        $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-GB');

        $data = new DataWrapper($contentCreateStruct, $contentCreateStruct->contentType);

        // No method to create named builder in framework controller
        /** @var $formBuilder \Symfony\Component\Form\FormBuilderInterface */
        $formBuilder = $this->container->get('form.factory')->createBuilder(CreateContentType::class, $data);
        // Adding controls as EzFormsBundle does not do that by itself
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
            'NetgenEzFormsBundle::demo_form.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    public function demoUpdateContentAction(Request $request)
    {
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();
        $repository->setCurrentUser(
            $repository->getUserService()->loadUserByLogin('admin')
        );
        $content = $contentService->loadContent(137);
        $contentType = $repository->getContentTypeService()->loadContentType($content->contentInfo->contentTypeId);
        $contentUpdateStruct = $contentService->newContentUpdateStruct();
        $contentUpdateStruct->initialLanguageCode = 'eng-GB';

        $data = new DataWrapper($contentUpdateStruct, $contentType, $content);

        // No method to create named builder in framework controller
        /** @var $formBuilder \Symfony\Component\Form\FormBuilderInterface */
        $formBuilder = $this->container->get('form.factory')->createBuilder(UpdateContentType::class, $data);
        // Adding controls as EzFormsBundle does not do that by itself
        $formBuilder->add('save', SubmitType::class, array('label' => 'Update'));

        $form = $formBuilder->getForm();
        //$form = $this->createForm(UpdateContentType::class, $data);
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
                // @todo do something else if needed
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
            'NetgenEzFormsBundle::demo_form.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    public function demoCreateUserAction(Request $request)
    {
        // @todo check that user really is anonymous, otherwise it does not make sense to allow registration

        $repository = $this->getRepository();
        $userService = $repository->getUserService();
        $repository->setCurrentUser(
            // @todo anonymous requires additional permissions to create new user
            $userService->loadUserByLogin('admin')
        );

        $contentType = $repository->getContentTypeService()->loadContentTypeByIdentifier('user');
        $userCreateStruct = $userService->newUserCreateStruct(
            null,
            null,
            null,
            'eng-GB',
            $contentType
        );
        // Setting manually as it is not controlled through form
        $userCreateStruct->enabled = false;

        $data = new DataWrapper($userCreateStruct, $userCreateStruct->contentType);

        // No method to create named builder in framework controller
        /** @var $formBuilder \Symfony\Component\Form\FormBuilderInterface */
        $formBuilder = $this->container->get('form.factory')->createBuilder(CreateUserType::class, $data);
        // Adding controls as EzFormsBundle does not do that by itself
        $formBuilder->add('save', SubmitType::class, array('label' => 'Publish'));

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            // @todo ensure that user can create 'user' type under required UserGroup Location
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
            'NetgenEzFormsBundle::demo_form.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    public function demoUpdateUserAction(Request $request)
    {
        $repository = $this->getRepository();
        $userService = $repository->getUserService();
        $contentService = $repository->getContentService();

        // @todo check that user is really logged in, it should have permissions to self edit
        $repository->setCurrentUser(
            $repository->getUserService()->loadUserByLogin('admin')
        );

        // @todo load current user
        $user = $userService->loadUser(142);
        $contentType = $repository->getContentTypeService()->loadContentTypeByIdentifier('user');
        $contentUpdateStruct = $contentService->newContentUpdateStruct();
        $contentUpdateStruct->initialLanguageCode = 'eng-GB';
        $userUpdateStruct = $userService->newUserUpdateStruct();
        $userUpdateStruct->contentUpdateStruct = $contentUpdateStruct;

        $data = new DataWrapper($userUpdateStruct, $contentType, $user);

        // No method to create named builder in framework controller
        /** @var $formBuilder \Symfony\Component\Form\FormBuilderInterface */
        $formBuilder = $this->container->get('form.factory')->createBuilder(UpdateUserType::class, $data);
        // Adding controls as EzFormsBundle does not do that by itself
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
            'NetgenEzFormsBundle::demo_form.html.twig',
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
