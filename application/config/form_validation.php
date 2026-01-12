<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$config = array(
        'login' => array(
                array(
                        'field' => 'username',
                        'label' => 'Username',
                        'rules' => 'required|alpha_numeric',
                        'errors'=> array(
                                        'required'      => 'You have not provided %s.',
                                        'tesxt'         => 'This %s already exists.Blah Blah'
                                    )
                ),
                array(
                        'field' => 'password',
                        'label' => 'Password',
                        'rules' => 'required',
                        'errors'=> array( 
                                        'required'      => 'You have not provided %s.' 
                                    )
                )
        ),
        'language' => array(
                array(
                        'field' => 'language',
                        'label' => 'language',
                        'rules' => 'required|alpha|is_unique[languages.language]',
                        'errors'=> array(
                                        'required'      => 'You have not provided %s.',
                                        'is_unique'     => 'Language already added.'
                                    )
                )
        )
);
