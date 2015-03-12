<img src="https://fluidtypo3.org/logo.svgz" width="100%" />

Fluidbackend: Flux Backend Modules
==================================

[![Build Status](https://img.shields.io/jenkins/s/https/jenkins.fluidtypo3.org/fluidbackend.svg?style=flat-square)](https://jenkins.fluidtypo3.org/job/fluidbackend/) [![Coverage Status](https://img.shields.io/coveralls/FluidTYPO3/fluidbackend/development.svg?style=flat-square)](https://coveralls.io/r/FluidTYPO3/fluidbackend)

> Fluidbackend uses Flux forms as backend modules which can save data to a multitude of targets and target types.

Concept
-------

Fluidbackend takes a Flux-enriched Fluid template similar to those used by other Flux features like Fluidcontent and Fluidpages,
and turns the Flux Form definition inside that template into a full-fledged backend module. And like Fluidcontent and Fluidpages,
Fluidbackend also requires that you ship your templates inside a so-called Provider Extension.

Fluidbackend requires a controller class - your module still has to be registered in TYPO3 as a proper backend module, but rather
than requiring you to enter all the configuration those modules need (a lot of boilerplate code), Fluidbackend uses Flux to read
the instructions for how to register the template as a backend module. You define these options inside the template and just like
with Fluidcontent and Fluidpages, an integrator can later replace these templates, add additional fields, data processing etc.

Fluidbackend was created to *fit the 80% of use cases* intentionally, to keep the complexity low. As such, it is not a replacement
for advanced backend modules - it is merely a way to very quickly create a module based on a form which saves/processes data.

Example
-------

These three pieces of code together make a new backend module through Fluidbackend:

```php
// ext_tables.php
\FluidTYPO3\Flux\Core::registerProviderExtensionKey('MyVendor.Myextension', 'Backend');
```

```php
// Classes/Controller/BackendController
namespace MyVendor\Myextension\Controller;
use FluidTYPO3\Fluidbackend\Controller\AbstractBackendController;
class BackendController extends AbstractBackendController {

}
```

```xml
<!-- Resources/Private/Templates/Backend/Example.html -->
{namespace flux=FluidTYPO3\Flux\ViewHelpers}
<f:layout name="Backend" />
<f:section name="Configuration">
    <flux:form id="mymodule">
        <!-- The options that govern integration -->
        <flux:form.option name="Fluidbackend" value="{
            moduleGroup: 'mygroupnameorexistinggroup',
            modulePosition: 'after:web',
            modulePageTree: 1}" />
        
        <!-- The fields that the form will contain -->
        <flux:field.input name="inputfield" />
        
        <!-- The pipe(s) which will process the data -->
        <flux:pipe.flashMessage title="Win!" message="You win!" />
    </flux:form>
</f:section>
<f:section name="Main">
	<!-- If you want some content before the form... -->
    <flux:form.render form="{form}" />
    <!-- ...or after it, here's the place to put it -->
</f:section>
```

How does it work
----------------

The developer flow of Fluidbackend is as follows:

1. Developer registers Provider Extension with Flux, with controller name `Backend`.
2. Developer creates form in template with module options and `Pipes` that process data.
3. Fluidbackend hooks into TYPO3 backend rendering, processing `Backend` Provider Extensions.
4. The Flux Form instance from each template is retrieved.
5. The options defined in this Form are used to give the module icon, placement, group etc.
6. The module is added to the list of available TYPO3 backend modules.
7. Steps 3-5 is repeated with every template file that contain a valid and enabled Form definition.

And the user flow is as follows:

1. The user enters the backend module.
2. The template that corresponds to that module is rendered.
3. The user fills in the form that was defined in the template.
4. The user submits the form data.
5. The `BackendController` action `saveAction` is called.
6. Fluidbackend reads the Form instance.
7. All in- and output processings defined in the Flux form as `Pipes` are executed.
8. The user is returned to the form.

In addition to passing the data through all `Pipes`, Fluidbackend will also store a database record (currently in PID zero). The
data stored in this record can then be retrieved. The data record uses the Extbase domain model principes and can be attached to
other domain records from other extensions, should you wish to bind a stored set of data to one or more of your model instances.

There are two ways to reach the stored data record manually:


```xml
<!-- in Fluid -->

{flux:flexform.data(table: 'tx_fluidbackend_domain_model_configuration',
	field: 'configuration', uid: uidOfSavedRecord) -> v:var.set(name: 'myData')}
```

```php
// In PHP

// $this->configurationRepository is an injected \FluidTYPO3\Fluidbackend\Domain\Repository\ConfigurationRepository
$uid = 123; // Uid of stored data record.
/** @var $settingsRecord \FluidTYPO3\Fluidbackend\Domain\Model\Configuration */
$settingsRecord = $this->configurationRepository->findByUid($uid);
$configuration = (array) $settingsRecord->getConfiguration();
```

What are "Pipes"?
-----------------

The `Pipes` are a concept from Flux which essentially define *some handling that accepts input and delivers output*. A `Pipe` may
change the data it receives or it may trigger some action without modifying the data. Inside, a `Pipe` is a simple class that has
a method which accepts the data as input and is expected to return the data, modified or not. Flux then provides ViewHelpers which
let developers associate `Pipes` with the Flux form. Each `Pipe` can be configured to act either before the controller action is
called or after it has been called, by use of a `direction` parameter.

When the `Pipes` are executed, the input data travels through each `Pipe` in the order they were defined. If a `Pipe` converts
the data, then the converted data is passed from that point onward and all the next `Pipes` receive the converted data only.

Because the `Pipes` are defined in the template they can be controlled with `f:if` conditions and each parameter of each `Pipe`
can be controlled individually. New `Pipes` can be added and existing ones removed/disabled.

Fluidbackend then uses this when the form data is submitted. The Form instance is retrieved and from it all `Pipe` instances are
fetched and processed. At any point can the `Pipes` be changed - for example, it can be done in the controller after data is sent.
`Pipes` can be accessed using `$form->getOutlet()->getPipesIn()` or `$form->getOutlet()->getPipesOut()`, into which `Pipes` are
grouped by their `direction` parameter.

Flux includes a few `Pipes`:

* TypeConverterPipe - converts the input to another output type.
* FlashMessagePipe - dispatches a FlashMessage with selected title/message/severity.
* EmailPipe - sends an email with selected subject/sender/recipient.
* ControllerPipe - calls a custom controller action with the posted data as input.

You, as a developer, can create and implement your own `Pipe` implementations. To do so:

1. To implement the `PipeInterface` from Flux.
2. To create a ViewHelper class that subclasses `AbstractFormViewHelper` from Flux.
3. To make that ViewHelper do `$this->getForm()->getOutlet()->addPipeIn($pipe)` (or `addPipeOut($pipe)`).
4. Alternatively, in your BackendController, `$this->provider->getForm($this->getRecord())->getOutlet()->addPipeIn($pipe)`. 

If you want your `Pipe` to be controlled through the template, add a ViewHelper for it. Otherwise, all you need is the class.
