<?php

class PageController extends Zend_Controller_Action {


    public function init() {
        $this->lang = $this->view->lang = $this->_helper->checklang->check();
        $this->location = $this->_helper->checklocation->check();
        $this->check_messages = $this->_helper->checkMessages;
        $this->notifications = $this->_helper->Notifications;

        //check if user is locked
        $locked = $this->_helper->checkLockedUser->check();
        if ($locked == 1) {
            $this->_redirect('/' . $this->view->lang . '/auth/logout');
        }
    }


    /*default action */
    public function indexAction() {
        $this->_redirect('/');
    }


    public function tosAction() {
        $this->view->page_title .= $this->view->translate('Terms of service');
    }


    public function privacyAction() {
        $this->view->page_title .= $this->view->translate('Privacy');
    }


    public function faqsAction() {
        $this->view->page_title .= $this->view->translate('Frequently asked questions');
    }


    public function aboutAction() {
        $this->view->page_title .= $this->view->translate('About nolotiro.org');
    }


    public function translateAction() {
        $this->view->page_title .= $this->view->translate('Help us to translate nolotiro.org to your language');

        $request = $this->getRequest();
        $newlangs = array('ca'=>'Català', 'gl'=>'Galego', 'eu'=>'Euskara',
                          'nl'=>'Nederlands', 'de'=>'Deutsch', 'fr'=>'Français',
                          'pt'=>'Português', 'it'=>'Italiano');

        $lform = new Zend_Form();
        $lform->setMethod('get');
        $lform->addElement('select', 'lang', 
                            array('multiOptions' => $newlangs));
        $elem_newlang = $lform->getElement('lang');
        $elem_newlang->removeDecorator('label')
                     ->removeDecorator('HtmlTag')
                     ->setAttrib('onchange', 'this.form.submit()');

        $lform->populate($request->getParams());
        $newlang = $elem_newlang->getValue();

        $this->view->langsform = $lform;

        if ($newlang != null) {
            if (isset($newlangs[$newlang]))
                $this->view->newlangtext = $newlangs[$newlang];
            else
                $newlang == null;
        }

        if ($newlang == null) {
            $elem_newlang->clearMultiOptions();
            $elem_newlang->addMultiOption("", "-- " . $this->view->translate("Choose language") . " --");
            $elem_newlang->addMultiOptions($newlangs);
        } else {
            $options = array('scan' => Zend_Translate::LOCALE_FILENAME);
            $translate = new Zend_Translate ('csv', NOLOTIRO_PATH . '/application/languages/', 'en', $options);
            $adapter = $translate->getAdapter();
            $es = $adapter->getMessages('es');
            if (strcmp($this->lang, $newlang) != 0) {
                $userlang = $adapter->getMessages($this->lang);
                $adapter->setLocale($this->lang);
            } else {
                $adapter->setLocale("en");
            }

            $en = $adapter->getMessages('en');

            $lang = $adapter->getMessages($newlang);

            $tform = new Zend_Form();
            $tform->setMethod('post');
            $tform->setAttrib('class', 'texts');
            $tform->addElement('captcha', 'safe_captcha', array (
                'label' => 'Please, insert the 4 characters shown:',
                'required' => true,
                'captcha' => array (
                        ' captcha' => 'Image',
                        'wordLen' => 4,
                        'height' => 50,
                        'width' => 160,
                        'gcfreq' => 50,
                        'timeout' => 300,
                        'font' => NOLOTIRO_PATH . '/www/images/antigonimed.ttf',
                        'imgdir' => NOLOTIRO_PATH . '/www/images/captcha')));
            $tform->setTranslator($adapter);

            $index = 0;
            foreach ($es as $key => $text) {
                if (strpos($key, "safe_") === 0) continue;

                if (isset($userlang[$key]))
                    $text = $userlang[$key];
                elseif (isset($en[$key]))
                    $text = $en[$key];
                else
                    $text = $key;

                $maxlen = strlen($text) * 3;
                $text = preg_replace("/(\<[^\>]*\>)/", " ", $text);
                $text = preg_replace("/(\'?%[a-zA-Z\-]*%?\'?)/", "...", $text);

                if (isset($lang[$key])) {
                    $val = $lang[$key];
                    $maxlen = strlen($val) * 3;

                    $val = preg_replace("/(\<[^\>]*\>)/", " ", $val);
                    $val = preg_replace("/(\'?%[a-zA-Z\-]*%?\'?)/", "...", $val);
                } else {
                    $val = '';
                }

                if ($maxlen < 20) $maxlen = 20;
                if ($maxlen < 140) {
                    $type = "text";
                    $rows = 1;
                } else {
                    $type = "textarea";
                    $rows = round($maxlen / 50);
                }

                $tform->addElement($type, "text$index", array (
                    'validators' => array (
                        array('StringLength', false, array(1, $maxlen))),
                    'required' => false,
                    'label' => $text,
                    'value' => $val,
                    'cols' => 40,
                    'rows' => $rows
                ));
                $input = $tform->getElement("text$index");
                if ($val == '')
                    $input->getDecorator('Label')->setOption('class', 'empty');
                $valid = $input->getValidator("StringLength")
                                ->setEncoding('UTF-8');
                $index++;
            }

            // add the submit button
            $tform->addElement('submit', 'submit_texts', array(
                'label' => 'Send texts',
                'class' => 'magenta awesome'
            ));
            $this->view->textsform = $tform;

            $this->view->newlang = $newlang;
            $tform->populate($request->getParams());

            if ($tform->getElement("submit_texts")->getValue() != null) {
                $data = $tform->getValues();
                if ($tform->isValid($data)) {
                    $newdata = false;
                    $index = 0;
                    foreach ($es as $key => $text) {
                        if (strpos($key, "safe_") === 0 ||
                            strpos($key, "lang") === 0) continue;

                        $mod = false;
                        $val = $data["text$index"];

                        $comp = $lang[$key];
                        $comp = preg_replace("/(\<[^\>]*\>)/", " ", $comp);
                        $comp = preg_replace("/(\'?%[a-zA-Z\-]*%?\'?)/", "...", $comp);

                        if ($val != "" && (!isset($lang[$key]) || ($mod = ($comp != $val)))) {
                            $body .= "\"$key\";\"$val\"" . ($mod ? ' ***' : "") . "<br>";
                            if ($mod) $body .= "\"$key\";\"$comp\"" . ($mod ? ' ***' : "") . "<br>";
                            $newdata = true;
                        }
                        else
                            $body .= "<br>";
                        $index++;
                    }

                    if (!$newdata) {
                        $this->view->error = $this->view->translate('Please, translate at least one text.');
                        return;
                    }

                    $mail = new Zend_Mail ('utf-8');
                    $mail->setBodyHtml($body);

                    $auth = Zend_Auth::getInstance();
                    if ($auth->hasIdentity()) {
                        $mail->setFrom($auth->getIdentity()->email, $auth->getIdentity()->username);
                    } else {
                        $mail->setFrom("noreply@nolotiro.org");
                    }

                    $mail->addTo('daniel.remeseiro@gmail.com');
                    $mail->setSubject("Translation: $newlang.csv");
                    $mail->send();
                    $this->_helper->_flashMessenger->addMessage($this->view->translate('Your translation has been sent. Thanks for your help!'));
                    $this->_redirect('/' . $this->lang);
                }
            }
        }
    }

}
