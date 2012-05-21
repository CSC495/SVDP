<?php

class Application_Model_Member_CommentsSubForm extends Twitter_Bootstrap_Form_Horizontal
{

    private $_userId;

    private $_comments;

    private $_readOnly;

    public function __construct($userId, array $comments, $readOnly = false)
    {
        $this->_userId   = $userId;
        $this->_comments = $comments;
        $this->_readOnly = $readOnly;

        parent::__construct(array(
            'decorators' => array(
                array('ViewScript', array(
                    'viewScript' => 'form/comments-sub-form.phtml',
                    'comments' => &$this->_comments,
                    'readOnly' => &$this->_readOnly,
                )),
            ),
        ));

        if (!$this->_readOnly) {
            $this->addElement('textarea', 'commentText', array(
                'label' => 'Comment',
                'required' => true,
                'filters' => array('StringTrim'),
                'validators' => array(
                    array('NotEmpty', true, array(
                        'type' => 'string',
                        'messages' => array('isEmpty' => 'You must enter a comment.'),
                    )),
                ),
                'dimension' => 7,
                'rows' => 4,
            ));

            $this->addElement('submit', 'addComment', array(
                'label' => 'Add Comment',
                'decorators' => array('ViewHelper'),
                'class' => 'btn btn-success',
            ));
        }
    }

    public function isAddCommentRequest(array $data)
    {
        return isset($data['addComment']);
    }

    public function getComment()
    {
        $user = new Application_Model_Impl_User();
        $user->setUserId($this->_userId);

        $comment = new Application_Model_Impl_Comment();
        $comment
            ->setUser($user)
            ->setDateTime(date('Y-m-d H:i:s'))
            ->setText($this->commentText->getValue());

        return $comment;
    }
}
