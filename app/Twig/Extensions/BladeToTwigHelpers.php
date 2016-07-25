<?php

    namespace App\Twig\Extensions;

    use App\User;
    use Twig_Extension;
    use Twig_SimpleFunction;

    use Auth;
    use Session;
    
    class BladeToTwigHelpers extends Twig_Extension
    {
        /**
         * Get the name.
         * @return string
         */
        public function getName()
        {
            return 'BladeToTwigHelpers';
        }

        /**
         * Twig Extension get functions inside the extension
         * @return array
         */
        public function getFunctions()
        {
            return [
                'title' => new Twig_SimpleFunction('title', [$this, 'getTitle'], ['is_safe' => ['html']]),
                'hasError' => new Twig_SimpleFunction('hasError', [$this, 'getHasError'], ['is_safe' => ['html']]),
                'getError' => new Twig_SimpleFunction('getError', [$this, 'getError'], ['is_safe' => ['html']]),
                'passwordResetLink' => new Twig_SimpleFunction('passwordResetLink', [$this, 'getPasswordResetLink'], ['is_safe' => ['html']]),
                'user' => new Twig_SimpleFunction('user', [$this, 'getUser'], ['is_safe' => ['html']]),
                'cdn' => new Twig_SimpleFunction('cdn', [$this, 'getCDNUrl'], ['is_safe' => ['html']]),
                'label' => new Twig_SimpleFunction('label', [$this, 'getLabel'], ['is_safe' => ['html']]),
                'language' => new Twig_SimpleFunction('language', [$this, 'getLanguage'], ['is_safe' => ['html']]),
            ];
        }

        /**
         * A helper to get the content delivery network url.
         * @param $value
         * @return string
         */
        public function getCDNUrl($value)
        {
            return env('APP_CDN', '/') . $value;
        }

        /**
         * A helper to get the user.
         * @return static
         */
        public function getUser()
        {
            if (Auth::check()) {
                $user = Auth::user();
                return $user;
            }
            return User::create([]);
        }

        /**
         * A helper to get a password reset link.
         * @return string
         */
        public function getPasswordResetLink()
        {
            $result = "";
            if (Auth::check()) {
                $user = Auth::user();
                $token = $user->getRememberToken();
                $email = $user->getEmailForPasswordReset();
                $result = url('password/reset', $token) . "?email=" . $email;
            }
            return $result;
        }

        /**
         * A helper to check if the error is set.
         * @param $haystack
         * @param $needle
         * @return bool
         */
        public function getHasError($haystack, $needle)
        {
            return ($haystack->has($needle)) ? true: false;
        }

        /**
         * A helper to get the error.
         * @param $haystack
         * @param $needle
         * @return mixed
         */
        public function getError($haystack, $needle)
        {
            return $haystack->first($needle);
        }

        /**
         * Get the language selected.
         * @return mixed
         */
        public function getLanguage()
        {
            if (Session::has('lang')) {
                return Session::get('lang');
            } else {
                Session::set('lang', 'en');
                Session::save();

                return Session::get('lang');
            }
        }

        /**
         * A small formatter to convert
         * @param $text
         * @return mixed
         */
        private function format($text)
        {
            // Strong
            $text1 = str_replace("[b]","<strong>", $text);
            $text2 = str_replace("[/b]","</strong>", $text1);

            // Italic
            $text3 = str_replace("[i]","<em>", $text2);
            $text4 = str_replace("[/i]","</em>", $text3);


            // Break line automaticly
            $text4a = str_replace("\r\n","<br>", $text4);
            $text4b = str_replace("\n","<br>", $text4a);

            // Colors
            $text4c = str_replace("[g]","<span style=\"color: green;\">", $text4b);
            $text4d = str_replace("[/g]","</span>", $text4c);
            $text4e = str_replace("[r]","<span style=\"color: red;\">", $text4d);
            $text4f = str_replace("[/r]","</span>", $text4e);
            $text4g = str_replace("[b]","<span style=\"color: blue;\">", $text4f);
            $text4h = str_replace("[/b]","</span>", $text4g);

            // Code
            $text5 = str_replace("[pre]","<pre>", $text4h);
            $text6 = str_replace("[/pre]","</pre>", $text5);

            // Date
            $text7 = str_replace("[date]",date('d/m/Y'), $text6);
            $text8 = str_replace("[year]",date('Y'), $text7);
            $text9 = str_replace("[month]",date('m'), $text8);
            $text10 = str_replace("[day]",date('d'), $text9);
            $text11 = str_replace("[time]",date('H:i:s'), $text10);
            $text12 = str_replace("[hour]",date('H'), $text11);
            $text13 = str_replace("[minute]",date('i'), $text12);
            $text14 = str_replace("[seconds]",date('s'), $text13);

            // MangoICT
            $text15 = str_replace("[mango-copy]","&copy " . date('Y') . " MangoICT.com ", $text14);

            // Replace style
            $text16 = preg_replace('/style="[a-zA-Z0-9:;\.\s\(\)\-\,]*"/i',"width=\"100%\"",$text15);
            return $text16;
        }

        /**
         * @param $value
         */
        public function getTitle($value)
        {
            
        }

        /**
         * Get label will get a label from the table labels, if it does not exists it will create new entry.
         * @param $value
         * @param string $tags
         * @param array $options
         * @return mixed|string
         */
        public function getLabel($value, $tags = '', $options = [ ] )
        {
            if (!isset($options['undefinedVisible'])) {
                $options['undefinedVisible'] = true;
            }
            if (!isset($options['format'])) {
                $options['format'] = true;
            }
            if (!isset($options['noedit'])) {
                $options['noedit'] = false;
            }

            $abMode = 'a';
            if (Session::has('abmode') === false) {
                Session::put('abmode', 'a');
            } else {
                $abMode = Session::get('abmode');
            }

            $dbSelect = \DB::select("SELECT * FROM labels WHERE name = ? AND ab = ?", array($value, $abMode));

            if (count($dbSelect) === 0) {
                $label = new CMS_Label();
                $label->name = $value;
                $label->nl = '[undefined: ' . $value . ']';
                $label->en = '[undefined: ' . $value . ']';
                $label->fr = '[undefined: ' . $value . ']';
                $label->it = '[undefined: ' . $value . ']';
                $label->de = '[undefined: ' . $value . ']';
                $label->es = '[undefined: ' . $value . ']';
                $label->tags = $tags;
                $label->ab = $abMode;
                $label->save();

                return ($options['undefinedVisible']) ? "[undefined: " . $value ."]" : "";
            } else {

                // A / B testing!
                $label = CMS_Label::whereRaw("name = ? AND ab = ?", array($value, $abMode))->first();
                $labelA = CMS_Label::whereRaw("name = ? AND ab = ?", array($value, 'a'))->first();

                // If B is undefined then select A if A is undefined then still select A.
                if (Session::has('swapmode')) {
                    switch ($this->getLanguage()) {
                        case 'nl':
                            $pos = strpos($label->nl, "[undefined");
                            if ($pos === false) {
                            } else {
                                $label = $labelA;
                            }
                            break;
                        case 'en':
                            $pos = strpos($label->en, "[undefined");
                            if ($pos === false) {
                            } else {
                                $label = $labelA;
                            }
                            break;
                        case 'fr':
                            $pos = strpos($label->fr, "[undefined");
                            if ($pos === false) {
                            } else {
                                $label = $labelA;
                            }
                            break;
                        case 'it':
                            $pos = strpos($label->it, "[undefined");
                            if ($pos === false) {
                            } else {
                                $label = $labelA;
                            }
                            break;
                        case 'es':
                            $pos = strpos($label->es, "[undefined");
                            if ($pos === false) {
                            } else {
                                $label = $labelA;
                            }
                            break;
                    }
                }

                // Save new tags if needed.
                if ($label->tags !== $tags) {
                    $label->tags = $tags;
                    $label->save();
                }

                // Get the content
                switch ($this->getLanguage()) {
                    case 'nl':
                        $pos = strpos($label->nl, "[undefined");
                        if ($pos === false) {
                            $content = ($options['format']) ? $this->format($label->nl) : $label->nl;
                        } else {
                            $content = ($options['undefinedVisible']) ? ($options['format']) ? $this->format($label->nl) : $label->nl : '';
                        }
                        break;
                    case 'en':
                        $pos = strpos($label->en, "[undefined");
                        if ($pos === false) {
                            $content = ($options['format']) ? $this->format($label->en) : $label->en;
                        } else {
                            $content = ($options['undefinedVisible']) ? ($options['format']) ? $this->format($label->en) : $label->en : '';
                        }
                        break;
                    case 'fr':
                        $pos = strpos($label->fr, "[undefined");
                        if ($pos === false) {
                            $content = ($options['format']) ? $this->format($label->fr) : $label->fr;
                        } else {
                            $content = ($options['undefinedVisible']) ? ($options['format']) ? $this->format($label->fr) : $label->fr : '';
                        }
                        break;
                    case 'de':
                        $pos = strpos($label->de, "[undefined");
                        if ($pos === false) {
                            $content = ($options['format']) ? $this->format($label->de) : $label->de;
                        } else {
                            $content = ($options['undefinedVisible']) ? ($options['format']) ? $this->format($label->de) : $label->de : '';
                        }
                        break;
                    case 'es':
                        $pos = strpos($label->es, "[undefined");
                        if ($pos === false) {
                            $content = ($options['format']) ? $this->format($label->es) : $label->es;
                        } else {
                            $content = ($options['undefinedVisible']) ? ($options['format']) ? $this->format($label->es) : $label->es : '';
                        }
                        break;
                    case 'it':
                        $pos = strpos($label->it, "[undefined");
                        if ($pos === false) {
                            $content = ($options['format']) ? $this->format($label->it) : $label->it;
                        } else {
                            $content = ($options['undefinedVisible']) ? ($options['format']) ? $this->format($label->it) : $label->it : '';
                        }
                        break;
                }

                return $content;
            }
        }
    }
