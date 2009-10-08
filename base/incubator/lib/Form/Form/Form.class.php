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

/**
 * Объектное представление POST-формы для автоматизированного контроля за соответствием
 * программного представления формы и свёрстанного html-кода
 */
abstract class Form extends HTML_MetaFormAction
{

	/**
	 * Context assigned for this form by the caller
	 * @var object
	 */
	protected $context;

	private $components = array();
	private $name;
	private $action;
	private $isNotified = false;

	/**
	 * Добавляет ошибку обработки формы
	 * @param string $id error identifier
	 * @param mixed $message опциональное описание ошибки
	 */
	final function addError($id, $message = true)
	{
		$this->validationError(null, array($id,$message));
	}

	/**
	 * @return array
	 */
	final function getErrors()
	{
		$errList = array();
		foreach( parent::getErrors() as $e )
		{
			$errList[ reset($e['message']) ] = sizeof($e['message']) > 2
				? $e['message']
				: reset($e['message']);
		}
		return $errList;
	}

	/**
	 * Determines whether the form has any errors
	 *
	 * @return boolean
	 */
	final function hasErrors()
	{
		return sizeof(parent::getErrors()) > 0 ? true : false;
	}

	/**
	 * Determines, whether the form was invoked
	 * @return bool
	 */
	final function isNotified()
	{
		return $this->isNotified;
	}

	/**
	 * Context to be used inside the form
	 */
	final function setContext($object)
	{
		$this->context = $object;
	}

	/**
	 * Создаёт новый экземпляр объектного представления формы
	 * @param string $name уникальное имя формы (служит для регистратора Form_Observer)
	 * @param string $action URI цели формы
	 * @param object $context контекст для обработчика форм
	 */
	function __construct($name, $action, $context = null)
	{
		$this->name = $name;
		$this->action = $action;
		$this->context = $context;
	}

	final function getName()
	{
		return $this->name;
	}

	final function getAction()
	{
		return $this->action;
	}

	/**
	 * @return Form
	 */
	final function addComponent(FormComponent $object)
	{
		Assert::isTrue(
			isset($this->components[$object->GetName()]),
			sprintf(
				'Duplicate form element %s with %s name',
				get_class($object), $object->getName()
			)
		);

		$this->components[$object->getName()] = $object;
		return $this;
	}

	/**
	 * @return Form
	 */
	final function dropComponent($name)
	{
		unset($this->components[$name]);
		return $this;
	}

	final function compare(array $htmlMetaForm_components)
	{
		$hiddenFields = array();
		foreach( $this->components as $form_component )
		{
			$name = $form_component->getName();
			if ( !isset($htmlMetaForm_components[$name]) )
			{
				//hidden fields could be added via signer engine, no need to require designer to add them
				Assert::isTrue(
					$form_component instanceof HiddenFormComponent,
					sprintf(
						'%s (name="%s") does not exist in form container named "%s"',
						get_class($form_component), $name, $this->getName()
					)
				);

				$hiddenFields[] = $form_component;
				$htmlMetaForm_components[$form_component->getName()] = array
				(
					"type" => $form_component->getType(),
				);
			}
			$htmlMetaForm_component =& $htmlMetaForm_components[$name];

			if ( $htmlMetaForm_component['type'] != $form_component->getType() )
			{
				Core::Error('Incompatibility: element %s should be %s, %s is given',
					$name,$form_component->GetType(),$htmlMetaForm_component);
			}

			//check the default values
			if ($form_component instanceof SelectFormComponent)
			{
				$array = $form_component->getPredefinedValues();
				$items =& $htmlMetaForm_component['items'];
				foreach ($array as $key)
				{
					Assert::isTrue(
						isset($items[$key]),
						sprintf(
							'"%s" key should be defined within "%s" form element',
							$key, $form_component->getName()
						)
					);
				}
			}
		}

		return $hiddenFields;
	}

	/**
	 * Вызывает форму на исполнение с предварительными проверками на коллизии, валидность данных.
	 * Если ошибок не обнаружено, вызывается соответсвующий обработчик Form_Element_Button
	 * @param HTML_MetaForm $mf обработчик
	 */
	final function call(HTML_MetaForm $mf)
	{
		parent::HTML_MetaFormAction($mf);
		$btnName = parent::process();
		$this->isNotified = true;
		foreach( $this->components as $component )
		{
			$component->validate($this);
		}
		if ( false === $this->hasErrors() )
		{
			if ( $this->components[$btnName] instanceof ButtonFormComponent )
			{
				$this->components[$btnName]->call($this);
			}
			else
			{
				//$this->AddError('form_no_handler',"There is no button handler for $btnName is specified");
				$this->defaultSubmitting($this);
			}
		}
	}

	/**
	 * Gets the form component
	 *
	 * @param string $componentName
	 * @return FormComponent
	 */
	function getComponent($componentName)
	{
		if (isset($this->components[$componentName]))
		{
			return $this->components[$componentName];
		}
		else
		{
			Debug::error('unknown compoentn %s', $componentName);
		}
	}

	/**
	 * Gets the list of component names
	 *
	 * @return array
	 */
	function getComponentNames()
	{
		return array_keys($this->components);
	}

	/**
	 * Заглушка, которая будет вызвана для первичной инициализации формы, т.е. когда входных
	 * данных конкретно для данной формы не обнаружено
	 */
	function init()
	{}

	/**
	 * Заглушка, которая будет вызвана если форма была "засабмичена" без кнопки-сабмиттера. В
	 * дикой природе встречается редко
	 */
	function defaultSubmitting(Form $owner)
	{}
}

?>