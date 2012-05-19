<?php

class Application_Model_Member_ViewClientForm extends Twitter_Bootstrap_Form_Horizontal
{

    public function __construct($userId, Application_Model_Impl_Client $client, array $cases,
        array $comments)
    {
        $baseUrl = new Zend_View_Helper_BaseUrl();

        parent::__construct(array(
            'action' => $baseUrl->baseUrl(
                App_Resources::MEMBER . '/viewClient/id/' . urlencode($client->getId())
            ),
            'method' => 'post',
            'class' => 'form-horizontal',
            'decorators' => array(
                'PrepareElements',
                array('ViewScript', array(
                    'viewScript' => 'form/view-client-form.phtml',
                    'client' => $client,
                    'cases' => $cases,
                )),
                'Form',
            ),
        ));

        $this->addSubForm(
            new Application_Model_Member_CommentsSubForm($userId, $comments),
            'commentsSubForm'
        );
    }

    public function isValid($data)
    {
        if ($this->commentsSubForm->isAddCommentRequest($data)) {
            return $this->commentsSubForm->isValid($data);
        }

        return true;
    }

    public function getAddedComment(array $data)
    {
        if ($this->commentsSubForm->isAddCommentRequest($data)) {
            return $this->commentsSubForm->getComment();
        }

        return null;
    }
}
