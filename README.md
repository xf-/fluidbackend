<img src="https://fluidtypo3.org/logo.svgz" width="100%" />

Fluidbackend: Flux Backend Modules
==================================

> **Fluid Backend** is an API for using Flux forms as backend modules which can save data to a multitude of targets and target types.

[![Build Status](https://img.shields.io/jenkins/s/https/jenkins.fluidtypo3.org/fluidbackend.svg?style=flat-square)](https://jenkins.fluidtypo3.org/job/fluidbackend/) [![Coverage Status](https://img.shields.io/coveralls/FluidTYPO3/fluidbackend/development.svg?style=flat-square)](https://coveralls.io/r/FluidTYPO3/fluidbackend)

## What does it do?

> EXT:fluidbackend saves data entered in a form created using Flux ViewHelpers - and saves it to any number of highly configurable
> target types (for example, JSON files or TypoScript records) as a data structure which reflects the field names.
>
> EXT:fluidbackend works much like EXT:fluidcontent and EXT:fluidpages by registering a set of templates which are to be used as
> individual backend modules - EXT:fluidbackend then renders the Flux form defined in each template and presents in as a backend
> module as the main interface.
>
> You can configure the module's position, icon etc. through the Fluid template - and you can create a custom menu group for your
> module(s), or you can place it in an existing group as a submodule of for example the "Web" menu group.

## Key concepts

The following terms are used to describe the features and concerns of EXT:fluidbackend

1. A **Fluidbackend Module** is a TYPO3 backend module created based on one Fluid template file
2. A **Flux form** is a set of fields (grouped as tabs if dessired) which can be given custom names, causing the data saved to be
   named accordingly. In EXT:fluidbackend, a **Flux form** can also contain any number of defined **Outlets**
3. An **Outlet** is a definition of one individual target in which the form data is saved; any number of similar or different
   **Outlets** can be defined in each **Flux form**, resulting in the form data being saved to each **Outlet** every time.
4. Built-in **Outlet Types** are located in EXT:fluidbackend/Classes/Outlet/*Outlet.php and use ViewHelpers named the same.
5. A **Configuration record** is a special type of TYPO3 (TCA-based) database record which contains only one field of the type
   `flex` - which is done to allow TYPO3 to process the field transparently as a (Flux-enabled) FlexForm field. This **Configuration
   record** and the configuration stored within can then be loaded from a Repository, used as relation, read from a ViewHelper etc.

Not exactly rocket science.

## How does it do it?

When posting data from this form-based module, EXT:fluidbackend has multiple modes of operation:

* It can call a custom controller with each field mapped to an argument - of course, dotted-path field names create an array which
  is then mapped to the data type required by the controller ation's argument (which means you can actually post a Model object
  with a form generated by a Flux form).
* It will always write a special type of configuration record which contains the configuration posted from the Flux form - this
  can then be read using various tools from EXT:fluidbackend. Of course the configuration record has an associated Extbase model
  and Repository and can be used in relations - the data of which can even be rendered inline from the record pointing to the
  configuration record.
* It can write TypoScript template records in any given location. One TypoScript record per Flux form can be created and
  automatically updated whenever data is changed in the EXT:fluidbackend generated module which uses the Flux form.
* It can write JSON files in any given location - again, one JSON file per Flux form, automatically maintained. This JSON file
  can then be read from frontend Javascripts, for example, to read configuration for an advanced FE module with a lot of scripts.
* Perhaps more not even considered - becase of the insane flexibility, you can use EXT:fluidbackend to write pretty much any type
  of configuration file. XML sources, PHP data-only files, XLF files and well, I suppose you could use it to generate new Flux
  based Fluid template files if you were to use Flux sections with objects (recursive nesting supported) to define new Flux forms.

Each type of target (JSON, TS, etc) is configured by inserting a ViewHelper (from EXT:fluidbackend) into the `<flux:flexform>`
tag - and add any number of additional tags to configure additional data writing targets, any number of targets per form. This is
the equivalent of calling multiple custom data saving methods from a custom controller, except it uses conventions for processing
the data to such a degree that exporting can be configured using simple ViewHelpers. Should you need an even more customised data
export functionality, you can easily configure one of your own controllers to be called when saving the form.

## Why use it?

There's no faster way to create backend modules which can output a multitude of data types. If you need a backend module to define
a configuration-type array of settings which are to be consumed by any source (since supporting JSON and allowing you to very
easily use any storage format when using a custom controller), this should be your one and only necesary stop.

The idea is extremely simple: use Flux to configure a form, supporting every TCEforms trick like wizards, sheets etc. - and grab
the output from that form, then save it to any number of destinations and destination types, according to how you configure your
template. It is even possible to use the form to configure the Outlets that the form data is saved to - not just as toggles, but
allowing things like setting the destination path of the JSON file, the storage pid of the TS record that gets saved etc.

## How does it work?

It's quite easy to grasp the infrastructure of EXT:fluidbackend:

* You register a template directory which is scanned for available templates. Since using Flux forms in those templates,
  EXT:fluidbackend can extract configuration from the template file
* This configuration can then be used to set up an automatic backend module which simply renders the configured Flux form
* Each template file becomes a backend module with its own icon and associated LLL file (_note: the LLL file is required by the
  TYPO3 core, unfortunately. It **must** be placed in EXT:extkey/Resources/Private/Language/locallang_module_BASENAMEOFTEMPLATE.xml
  and there MUST be one file per backend module template_)
* When posted, the data is intercepted by EXT:fluidbackend - if a controller is associated with the particular form, that controller
  is then called (with the data mapped as proper Extbase controller arguments of the type required by the controller)
* If no controller is associated, settings define how and where EXT:fluidbackend stores the data it received
* Output targets are defined as so-called Outlets which are defined using ViewHelpers and processed by a simple class which uses
  a shared interface. You define each Outlet exactly like you would define a Flux field, just using a different set of ViewHelpers
* Any number of Outlets of multiple types can be defined per backend form, each one will be executed sequentially when saving data
* The included Outlets can save to the special configuration record type, to JSON/P, to PHP and to TypoScript (sys_template or file)
* New Outlet types can easily be added by simply creating one custom ViewHelper with one associated Outlet type - accepting any
  additional arguments which your Outlet may need

> Tip: You can subclass the ViewHelper and Outlet classes to create encrypted storage methods using your favorite encryption.

> Tip: You can use field names like `plugin.tx_myext.settings.foobar` and choose a TypoScript outlet - be it a record or a file
> which you later include - to write highly flexible interfaces to store settings for your plugins in a dedicated backend module.

## Examples

### An extremely minimal template and controller example

```
{namespace flux=Tx_Flux_ViewHelpers}
{namespace be=Tx_Fluidbackend_ViewHelpers}
<f:layout name="Default" />

<f:section name="Configuration">
	<flux:flexform id="render" label="Backend test" icon="EXT:fluidbackend/ext_icon.gif">
		<!-- the be:module ViewHelper configures this module's position, parent and
			 wether to use the page tree component (the "navigation" attribute) -->
		<be:module group="mygroup" position="after:web" navigation="FALSE" />
		<be:outlet.json name="json" label="JSON output" path="uploads/testjson.json" />
	</flux:flexform>
</f:section>

<f:section name="Main">
	<be:form />
</f:section>

<f:section name="ButtonsLeft">

</f:section>

<f:section name="ButtonsRight">

</f:section>

<f:section name="Status">

</f:section>
```

Note that you don't need to use all sections mentioned above - they are included to illustrate the full possibilities. This
example does not contain any fields, but had there been any fields present, their values would be saved to the JSON file as defined.

### Required extension key registration

EXT:fluidbackend must, through Flux, be able to gather a list of extension keys which want to register their templates. This list
must include your extension - and to add your extension key (VendorName.ExtensionName supported) use this line in `ext_tables.php`
of your own extension:

```
Tx_Flux_Core::registerProviderExtensionKey('myextensionkey', 'Backend');
```

This lets Flux store your extension key and lets Fluidbackend retrieve it just before rendering the backend, then detecting
templates in the path containing action templates for your BackendController - a controller class you are also required to use.

### Required language file

In order to display proper labels for the module menu in the TYPO3 backend, TYPO3 needs to know the location of an LLL file with
the labels describing the module. Such a file __must__ be located in EXT:myext/Resources/Private/Language/locallang_module_foobar.xml
where "foobar" is the action to which the template corresponds. One file must be used per template.

```
<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<T3locallang>
	<meta type="array">
		<type>database</type>
		<description>Language labels for backend module 'foobar' belonging to extension 'myext'</description>
	</meta>
	<data type="array">
		<languageKey index="default" type="array">
			<label index="mlang_tabs_tab">My module</label>
			<label index="mlang_labels_tablabel">My module does things</label>
		</languageKey>
	</data>
</T3locallang>
```

Naturally this LLL file uses the language of the backend user - or the default. Exactly like TYPO3 always does.

> Note: If you want your Fluid Backend module to be placed in a custom group (like the Web, File, Admin etc. groups) then simply
> use the `<be:module group="mygroup" />` ViewHelper and place an LLL file just like the one above, except in this location:
> EXT:myext/Resources/Private/Language/locallang_module_GROUPNAME.xml (in thise example it would be locallang_module_mygroup.xml).

### Required controller class

EXT:fluidbackend will not work without a "BackendController" being present in the extension which provides the module(s). Such a
controller class must subclass the correct class in order to work.

```
class Tx_Myext_Controller_BackendController extends Tx_Fluidbackend_Controller_AbstractBackendController {

}
```

In this controller you have a number of methods available, but do not need to configure any actions at all. The default behavior
will then be to render the "Render.html" template file from the `templateRootPath`/Backend/Render.html path (in other words: if
you need just a single module, use the filename "Render.html" - for more modules, add additional files).

For a full picture of which pre/post methods are available, check the parent class (AbstractBackendController). The doc comments
for each method clearly states when the method is executed and what it must return.

### Outlet examples

Inside a standard `<flux:flexform>` tag you may define a standard Flux form and in addition to every Flux ViewHelper, you can also
use a few Fluidbackend-specific ViewHelpers. The following example saves the form data as:

* A JSON data file
* A TypoScript constants array
* Custom output by dispatching an internal Request to `Tx_Myext_Controller_EsotericController->magicAction`

When saved, all Outlets are updated with the same information. The controller action is called with one parameter enforced,
$settings (array), and any number of additional, optional or required arguments (which must then be added in the ViewHelper tag
attribute "arguments" for the controller outlet).

```
<be:outlet.json path="uploads/tx_myext/data.json" name="json" label="JSON data file" />
<be:outlet.typoScript name="ts" label="TypoScript" constants="TRUE" />
<be:outlet.controller extensionKey="myext" controller="Esoteric" action="magic" arguments="{customArgument: 'specialValue')}" />
```

The proper controller action method signature to accept this controller dispatch would be:

```
/**
 * @param array $settings Master settings array based on form data
 * @param string $specialValue Additional argument configured in the Outlet instead of form data
 */
public function magicAction(array $settings, $specialValue) {
	// perform astounding magic tricks with the posted data
	// a small report is built as $briefReportString and the
	// contents of this string are reported as a FlasMessage.
	return 'Done performing magic. The result: ' . $briefReportString;
}
```

These outlet products can be consumed by any number of sources. For TypoScript specifically, I highly recommend that you use the
naming scope requirements which make the values appear "automagically" in your template/controller as the "settings" array. Other
examples include:

#### Consuming JSON using jQuery

```
jQuery.ajax({url: 'uploads/tx_myext/data.json', complete: function() {
	doStuffWithData();
}});
```

#### Using Flux to read from the storage database

```
{flux:flexform.data(table: 'tx_fluidbackend_domain_model_configuration',
	field: 'configuration', uid: uidOfSavedRecord) -> v:var.set(name: 'myData')}
```

#### Using the Repository

```
// Uid from FlexForm - but could also be TS, an EM setting, a relation from another object etc.
$uid = $this->settings['flexform']['appliedConfigurationRecord'];
/** @var $settingsRecord Tx_Fluidbackend_Domain_Model_Configuration */
$settingsRecord = $this->configurationRepository->findByUid($uid);
/** @var $configuration array */
$configuration = $settingsRecord->getConfiguration();
```

You'll want to switch between these methods depending on the type of source being consumed, but there's no reason why you cannot
use PHP to consume a JSON data file, for example. Just keep in mind that while PHP is protected from prying eyes, JSON is not -
unless you take measures to protect the file, in which case PHP would probably make more sense.

> Tip: When saving as PHP it becomes possible to save your file as protected values easily consumed by other sources, which makes
> it ideal to store things like sensitive passwords, API credentials, long-term tokens etc.

## Inspirations

* Create an Outlet which uses a remote API to store the settings?
* Create a list of Flux fields based on emails from an IMAP inbox - save JSON list of todo-tasks with priority, assigned person etc.

## Common pitfalls

* Labels for the menu items must be placed in EXT:extkey/Resources/Private/Language/locallang_module_BASENAMEOFTEMPLATE.xml and
  the two main labels must be named `mlang_tabs_tab` (main title) and `mlang_labels_tablabel` (secondary title)
* If you use a custom module group (group in menu) you'll need to add the LLL file (containing same labels as above):
  EXT:extkey/Resources/Private/Language/locallang_module_GROUPNAME.xml
* If you don't configure an Outlet for the data then nothing will happen when saving the form (except for updating config storage)
* If you require a custom controller to be called, use the `ControllerOutlet` and set the parameters identifying your controller.
