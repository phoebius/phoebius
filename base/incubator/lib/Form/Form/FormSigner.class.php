<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

require_once(PHOEBIUS_BASE_ROOT . '/3rdparty/HTML_MetaForm/HTML_SemiParser.php');
require_once(PHOEBIUS_BASE_ROOT . '/3rdparty/HTML_MetaForm/HTML_FormPersister.php');
require_once(PHOEBIUS_BASE_ROOT . '/3rdparty/HTML_MetaForm/HTML_MetaForm.php');
require_once(PHOEBIUS_BASE_ROOT . '/3rdparty/HTML_MetaForm/HTML_MetaFormAction.php');

/**
 * Организует автоматизированный регистратор входящих/выходящих форм, зарегистрированных в
 * системе: подписывает формы для того чтобы избежать их подделки, вызывает объекты входящих форм
 */
final class FormSigner extends HTML_MetaForm
{
	/**
	 * Hidden-поле для хранения идентификатора формы
	 */
	const ID_ELT = 'foid';

	/**
	 * Hidden-поле для хранения ключа формы
	 */
	const SIGN_ELT = 'soid';

	/**
	 * Создаёт новый автоматизированный регистратор форм
	 * @param string $uri URI для текущего запроса
	 */
	function __construct()
	{
		parent::HTML_MetaForm(Session::userSupertag());
		$this->MF_META_ELT = self::SIGN_ELT;
		//do not use sessions to avoid possible collisions
		$this->MF_USE_SESSION = false;
	}

	function process($s,$sign = false)
	{
		if ( $sign )
		{
			$s = HTML_FormPersister::ob_formPersisterHandler($s);
		}
		return parent::process($s);
	}

	/**
	 * Переписанный подписывальщик форм.
	 */
	function postprocText($text)
    {
        // Split text by forms.
        $formChunks = preg_split('/('.$this->_mf_hash.'form\d+\|)/s', $text, 0, PREG_SPLIT_DELIM_CAPTURE);
        $text = $formChunks[0];
        // Remove hashes from text outside all the forms.
        $this->_getMetasInText($text);
        // Now process each form separately.
        for ($i=1, $n=count($formChunks); $i<$n; $i+=2) {
            $hash = $formChunks[$i];
            $content = $formChunks[$i+1];
            // This form tag.
            $formTag = $this->_mf_collectForms[$hash];

            // Extract stored hashes for form fields & clean hashes from text.
            $metas = $this->_getMetasInText($content);

			/// Step 0. Check whether the "name" attribute within <form> container is provided
			if ( empty($formTag['name']) )
			{
				Core::Error(
					'Container &lt;form&gt; should have a "name" attribute ' .
					'with the unique identifier that is registered in global form pool '.
					'(Form_Observer)'
				);
			}

			$form = FormObserver::getForm($formTag['name']);

			if ( $formTag['action'] != $form->getAction() )
			{
				Debug::error('Invalid form action is specified: %s found, but %s is expected',
					$formTag['action'],
					$form->getAction());
			}

            /// Step 2. We sign the form name to invoke an appropriate form object
            $metas['items'][self::ID_ELT] = array
            (
            	"type" => "text",
            	"original" => $formTag['name'],
            	"name" => self::ID_ELT
            );

            /// Step 3. Ok, now we compare the form object and the elements found in the container
			$components = array();
			foreach( $metas['items'] as $meta )
			{
				$components[$meta['name']] = $meta;
			}
			$hiddenComponents = $form->compare($components);

            // Process only POST forms!
            if (strtoupper(@$formTag['method']) == 'POST') {
                // Generate hidden tag.
                $packed = $this->_packMeta($metas);
                if ($this->MF_USE_SESSION) {
                    // If session is used, store data in session.
                    $contentHash = $this->_getHashcode($packed);
                    $_SESSION[$this->MF_META_ELT][$contentHash] = $packed;
                    $packed = $contentHash;
                }
                // Add suffix (e.g. - current timestamp) to metadata as comment. This
                // should help debugging: we always know when form metadata was generated
                // and detect stupid proxy requests when page is cached for weeks. This
                // date must NOT be included in digital signature!
                if ($this->MF_SIGN_SUFFIX !== null) {
                    $packed .= " " . $this->MF_SIGN_SUFFIX;
                }
                $hidden = array(
                    '_tagName' => 'input',
                    'type'     => 'hidden',
                    'name'     => $this->MF_META_ELT,
                    'value'    => $packed,
                    '_text'    => null,
                );
                $text .= $this->makeTag($hidden);

                /// So right now we add the signed element containing the ID of a form
                $hidden = array(
                    '_tagName' => 'input',
                    'type'     => 'hidden',
                    'name'     => self::ID_ELT,
                    'value'    => $formTag['name'],
                    '_text'    => null,
                );
                $text .= $this->makeTag($hidden);

                foreach ((array)$hiddenComponents as $component)
                {
                	$defVal = $component->getDefaultValue();
                	$tag = array(
                		'_tagName' => 'input',
                		'type'     => 'hidden',
                		'name'     => $component->getName(),
                		'value'    => is_null($defVal)
                			? @$_POST[$component->getName()]
                			: $defVal,
                		'_text'    => null,
                	);
                	$text .= $this->makeTag($tag);
                }
            }

            $text .= $content;
        }
        return $text;
    }

}

?>