> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente: https://getbootstrap.com/docs/4.5/components/input-group/
> Capturado: 2026-08-21

Skip to main contentSkip to docs navigation

[There's a newer version of Bootstrap!](https://getbootstrap.com/)

[Bootstrap](/)

  * [Home](/)
  * [Documentation](/docs/4.5/getting-started/introduction/)
  * [Examples](/docs/4.5/examples/)
  * [Icons](https://icons.getbootstrap.com/)
  * [Expo](https://expo.getbootstrap.com/)
  * [Blog](https://blog.getbootstrap.com/)



  * v4.5 

[Latest (4.5.x)](/docs/4.5/)

[v3.4.1](https://getbootstrap.com/docs/3.4/) [v2.3.2](https://getbootstrap.com/2.3.2/)

[All versions](/docs/versions/)

  * [GitHub](https://github.com/twbs)
  * [Twitter](https://twitter.com/getbootstrap)
  * [Open Collective](https://opencollective.com/bootstrap/)

[Download](/docs/4.5/getting-started/download/)

Menu

[ Getting started ](/docs/4.5/getting-started/introduction/)

  * [ Introduction ](/docs/4.5/getting-started/introduction/)
  * [ Download ](/docs/4.5/getting-started/download/)
  * [ Contents ](/docs/4.5/getting-started/contents/)
  * [ Browsers & devices ](/docs/4.5/getting-started/browsers-devices/)
  * [ JavaScript ](/docs/4.5/getting-started/javascript/)
  * [ Theming ](/docs/4.5/getting-started/theming/)
  * [ Build tools ](/docs/4.5/getting-started/build-tools/)
  * [ Webpack ](/docs/4.5/getting-started/webpack/)
  * [ Accessibility ](/docs/4.5/getting-started/accessibility/)



[ Layout ](/docs/4.5/layout/overview/)

  * [ Overview ](/docs/4.5/layout/overview/)
  * [ Grid ](/docs/4.5/layout/grid/)
  * [ Utilities for layout ](/docs/4.5/layout/utilities-for-layout/)



[ Content ](/docs/4.5/content/reboot/)

  * [ Reboot ](/docs/4.5/content/reboot/)
  * [ Typography ](/docs/4.5/content/typography/)
  * [ Code ](/docs/4.5/content/code/)
  * [ Images ](/docs/4.5/content/images/)
  * [ Tables ](/docs/4.5/content/tables/)
  * [ Figures ](/docs/4.5/content/figures/)



[ Components ](/docs/4.5/components/alerts/)

  * [ Alerts ](/docs/4.5/components/alerts/)
  * [ Badge ](/docs/4.5/components/badge/)
  * [ Breadcrumb ](/docs/4.5/components/breadcrumb/)
  * [ Buttons ](/docs/4.5/components/buttons/)
  * [ Button group ](/docs/4.5/components/button-group/)
  * [ Card ](/docs/4.5/components/card/)
  * [ Carousel ](/docs/4.5/components/carousel/)
  * [ Collapse ](/docs/4.5/components/collapse/)
  * [ Dropdowns ](/docs/4.5/components/dropdowns/)
  * [ Forms ](/docs/4.5/components/forms/)
  * [ Input group ](/docs/4.5/components/input-group/)
  * [ Jumbotron ](/docs/4.5/components/jumbotron/)
  * [ List group ](/docs/4.5/components/list-group/)
  * [ Media object ](/docs/4.5/components/media-object/)
  * [ Modal ](/docs/4.5/components/modal/)
  * [ Navs ](/docs/4.5/components/navs/)
  * [ Navbar ](/docs/4.5/components/navbar/)
  * [ Pagination ](/docs/4.5/components/pagination/)
  * [ Popovers ](/docs/4.5/components/popovers/)
  * [ Progress ](/docs/4.5/components/progress/)
  * [ Scrollspy ](/docs/4.5/components/scrollspy/)
  * [ Spinners ](/docs/4.5/components/spinners/)
  * [ Toasts ](/docs/4.5/components/toasts/)
  * [ Tooltips ](/docs/4.5/components/tooltips/)



[ Utilities ](/docs/4.5/utilities/borders/)

  * [ Borders ](/docs/4.5/utilities/borders/)
  * [ Clearfix ](/docs/4.5/utilities/clearfix/)
  * [ Close icon ](/docs/4.5/utilities/close-icon/)
  * [ Colors ](/docs/4.5/utilities/colors/)
  * [ Display ](/docs/4.5/utilities/display/)
  * [ Embed ](/docs/4.5/utilities/embed/)
  * [ Flex ](/docs/4.5/utilities/flex/)
  * [ Float ](/docs/4.5/utilities/float/)
  * [ Image replacement ](/docs/4.5/utilities/image-replacement/)
  * [ Interactions ](/docs/4.5/utilities/interactions/)
  * [ Overflow ](/docs/4.5/utilities/overflow/)
  * [ Position ](/docs/4.5/utilities/position/)
  * [ Screen readers ](/docs/4.5/utilities/screen-readers/)
  * [ Shadows ](/docs/4.5/utilities/shadows/)
  * [ Sizing ](/docs/4.5/utilities/sizing/)
  * [ Spacing ](/docs/4.5/utilities/spacing/)
  * [ Stretched link ](/docs/4.5/utilities/stretched-link/)
  * [ Text ](/docs/4.5/utilities/text/)
  * [ Vertical align ](/docs/4.5/utilities/vertical-align/)
  * [ Visibility ](/docs/4.5/utilities/visibility/)



[ Extend ](/docs/4.5/extend/approach/)

  * [ Approach ](/docs/4.5/extend/approach/)
  * [ Icons ](/docs/4.5/extend/icons/)



[ Migration ](/docs/4.5/migration/)




[ About ](/docs/4.5/about/overview/)

  * [ Overview ](/docs/4.5/about/overview/)
  * [ Team ](/docs/4.5/about/team/)
  * [ Brand ](/docs/4.5/about/brand/)
  * [ License ](/docs/4.5/about/license/)
  * [ Translations ](/docs/4.5/about/translations/)



  * Basic example
  * Wrapping
  * Sizing
  * Checkboxes and radios
  * Multiple inputs
  * Multiple addons
  * Button addons
  * Buttons with dropdowns
  * Segmented buttons
  * Custom forms
    * Custom select
    * Custom file input
  * Accessibility



[View on GitHub](https://github.com/twbs/bootstrap/blob/v4.5.3/site/docs/4.5/components/input-group.md "View and edit this file on GitHub")

# Input group

Easily extend form controls by adding text, buttons, or button groups on either side of textual inputs, custom selects, and custom file inputs.

## Basic example

Place one add-on or button on either side of an input. You may also place one on both sides of an input. Remember to place `<label>`s outside the input group.

@

@example.com

Your vanity URL

https://example.com/users/

$

.00

With textarea
    
    
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text" id="basic-addon1">@</span>
      </div>
      <input type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
    </div>
    
    <div class="input-group mb-3">
      <input type="text" class="form-control" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="basic-addon2">
      <div class="input-group-append">
        <span class="input-group-text" id="basic-addon2">@example.com</span>
      </div>
    </div>
    
    <label for="basic-url">Your vanity URL</label>
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text" id="basic-addon3">https://example.com/users/</span>
      </div>
      <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3">
    </div>
    
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text">$</span>
      </div>
      <input type="text" class="form-control" aria-label="Amount (to the nearest dollar)">
      <div class="input-group-append">
        <span class="input-group-text">.00</span>
      </div>
    </div>
    
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text">With textarea</span>
      </div>
      <textarea class="form-control" aria-label="With textarea"></textarea>
    </div>

## Wrapping

Input groups wrap by default via `flex-wrap: wrap` in order to accommodate custom form field validation within an input group. You may disable this with `.flex-nowrap`.

@
    
    
    <div class="input-group flex-nowrap">
      <div class="input-group-prepend">
        <span class="input-group-text" id="addon-wrapping">@</span>
      </div>
      <input type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="addon-wrapping">
    </div>

## Sizing

Add the relative form sizing classes to the `.input-group` itself and contents within will automatically resize—no need for repeating the form control size classes on each element.

**Sizing on the individual input group elements isn’t supported.**

Small

Default

Large
    
    
    <div class="input-group input-group-sm mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text" id="inputGroup-sizing-sm">Small</span>
      </div>
      <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
    </div>
    
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text" id="inputGroup-sizing-default">Default</span>
      </div>
      <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
    </div>
    
    <div class="input-group input-group-lg">
      <div class="input-group-prepend">
        <span class="input-group-text" id="inputGroup-sizing-lg">Large</span>
      </div>
      <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg">
    </div>

## Checkboxes and radios

Place any checkbox or radio option within an input group’s addon instead of text.
    
    
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <div class="input-group-text">
          <input type="checkbox" aria-label="Checkbox for following text input">
        </div>
      </div>
      <input type="text" class="form-control" aria-label="Text input with checkbox">
    </div>
    
    <div class="input-group">
      <div class="input-group-prepend">
        <div class="input-group-text">
          <input type="radio" aria-label="Radio button for following text input">
        </div>
      </div>
      <input type="text" class="form-control" aria-label="Text input with radio button">
    </div>

## Multiple inputs

While multiple `<input>`s are supported visually, validation styles are only available for input groups with a single `<input>`.

First and last name
    
    
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text">First and last name</span>
      </div>
      <input type="text" aria-label="First name" class="form-control">
      <input type="text" aria-label="Last name" class="form-control">
    </div>

## Multiple addons

Multiple add-ons are supported and can be mixed with checkbox and radio input versions.

$ 0.00

$ 0.00
    
    
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text">$</span>
        <span class="input-group-text">0.00</span>
      </div>
      <input type="text" class="form-control" aria-label="Dollar amount (with dot and two decimal places)">
    </div>
    
    <div class="input-group">
      <input type="text" class="form-control" aria-label="Dollar amount (with dot and two decimal places)">
      <div class="input-group-append">
        <span class="input-group-text">$</span>
        <span class="input-group-text">0.00</span>
      </div>
    </div>

## Button addons

Button

Button

Button Button

Button Button
    
    
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <button class="btn btn-outline-secondary" type="button" id="button-addon1">Button</button>
      </div>
      <input type="text" class="form-control" placeholder="" aria-label="Example text with button addon" aria-describedby="button-addon1">
    </div>
    
    <div class="input-group mb-3">
      <input type="text" class="form-control" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="button-addon2">
      <div class="input-group-append">
        <button class="btn btn-outline-secondary" type="button" id="button-addon2">Button</button>
      </div>
    </div>
    
    <div class="input-group mb-3">
      <div class="input-group-prepend" id="button-addon3">
        <button class="btn btn-outline-secondary" type="button">Button</button>
        <button class="btn btn-outline-secondary" type="button">Button</button>
      </div>
      <input type="text" class="form-control" placeholder="" aria-label="Example text with two button addons" aria-describedby="button-addon3">
    </div>
    
    <div class="input-group">
      <input type="text" class="form-control" placeholder="Recipient's username" aria-label="Recipient's username with two button addons" aria-describedby="button-addon4">
      <div class="input-group-append" id="button-addon4">
        <button class="btn btn-outline-secondary" type="button">Button</button>
        <button class="btn btn-outline-secondary" type="button">Button</button>
      </div>
    </div>

## Buttons with dropdowns

Dropdown

Action Another action Something else here

Separated link

Dropdown

Action Another action Something else here

Separated link
    
    
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</button>
        <div class="dropdown-menu">
          <a class="dropdown-item" href="#">Action</a>
          <a class="dropdown-item" href="#">Another action</a>
          <a class="dropdown-item" href="#">Something else here</a>
          <div role="separator" class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Separated link</a>
        </div>
      </div>
      <input type="text" class="form-control" aria-label="Text input with dropdown button">
    </div>
    
    <div class="input-group">
      <input type="text" class="form-control" aria-label="Text input with dropdown button">
      <div class="input-group-append">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</button>
        <div class="dropdown-menu">
          <a class="dropdown-item" href="#">Action</a>
          <a class="dropdown-item" href="#">Another action</a>
          <a class="dropdown-item" href="#">Something else here</a>
          <div role="separator" class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Separated link</a>
        </div>
      </div>
    </div>

## Segmented buttons

Action Toggle Dropdown

Action Another action Something else here

Separated link

Action Toggle Dropdown

Action Another action Something else here

Separated link
    
    
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <button type="button" class="btn btn-outline-secondary">Action</button>
        <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div class="dropdown-menu">
          <a class="dropdown-item" href="#">Action</a>
          <a class="dropdown-item" href="#">Another action</a>
          <a class="dropdown-item" href="#">Something else here</a>
          <div role="separator" class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Separated link</a>
        </div>
      </div>
      <input type="text" class="form-control" aria-label="Text input with segmented dropdown button">
    </div>
    
    <div class="input-group">
      <input type="text" class="form-control" aria-label="Text input with segmented dropdown button">
      <div class="input-group-append">
        <button type="button" class="btn btn-outline-secondary">Action</button>
        <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div class="dropdown-menu">
          <a class="dropdown-item" href="#">Action</a>
          <a class="dropdown-item" href="#">Another action</a>
          <a class="dropdown-item" href="#">Something else here</a>
          <div role="separator" class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Separated link</a>
        </div>
      </div>
    </div>

## Custom forms

Input groups include support for custom selects and custom file inputs. Browser default versions of these are not supported.

### Custom select

Options

Choose... One Two Three

Choose... One Two Three

Options

Button

Choose... One Two Three

Choose... One Two Three

Button
    
    
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <label class="input-group-text" for="inputGroupSelect01">Options</label>
      </div>
      <select class="custom-select" id="inputGroupSelect01">
        <option selected>Choose...</option>
        <option value="1">One</option>
        <option value="2">Two</option>
        <option value="3">Three</option>
      </select>
    </div>
    
    <div class="input-group mb-3">
      <select class="custom-select" id="inputGroupSelect02">
        <option selected>Choose...</option>
        <option value="1">One</option>
        <option value="2">Two</option>
        <option value="3">Three</option>
      </select>
      <div class="input-group-append">
        <label class="input-group-text" for="inputGroupSelect02">Options</label>
      </div>
    </div>
    
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <button class="btn btn-outline-secondary" type="button">Button</button>
      </div>
      <select class="custom-select" id="inputGroupSelect03" aria-label="Example select with button addon">
        <option selected>Choose...</option>
        <option value="1">One</option>
        <option value="2">Two</option>
        <option value="3">Three</option>
      </select>
    </div>
    
    <div class="input-group">
      <select class="custom-select" id="inputGroupSelect04" aria-label="Example select with button addon">
        <option selected>Choose...</option>
        <option value="1">One</option>
        <option value="2">Two</option>
        <option value="3">Three</option>
      </select>
      <div class="input-group-append">
        <button class="btn btn-outline-secondary" type="button">Button</button>
      </div>
    </div>

### Custom file input

Upload

Choose file

Choose file

Upload

Button

Choose file

Choose file

Button
    
    
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text" id="inputGroupFileAddon01">Upload</span>
      </div>
      <div class="custom-file">
        <input type="file" class="custom-file-input" id="inputGroupFile01" aria-describedby="inputGroupFileAddon01">
        <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
      </div>
    </div>
    
    <div class="input-group mb-3">
      <div class="custom-file">
        <input type="file" class="custom-file-input" id="inputGroupFile02">
        <label class="custom-file-label" for="inputGroupFile02" aria-describedby="inputGroupFileAddon02">Choose file</label>
      </div>
      <div class="input-group-append">
        <span class="input-group-text" id="inputGroupFileAddon02">Upload</span>
      </div>
    </div>
    
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <button class="btn btn-outline-secondary" type="button" id="inputGroupFileAddon03">Button</button>
      </div>
      <div class="custom-file">
        <input type="file" class="custom-file-input" id="inputGroupFile03" aria-describedby="inputGroupFileAddon03">
        <label class="custom-file-label" for="inputGroupFile03">Choose file</label>
      </div>
    </div>
    
    <div class="input-group">
      <div class="custom-file">
        <input type="file" class="custom-file-input" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04">
        <label class="custom-file-label" for="inputGroupFile04">Choose file</label>
      </div>
      <div class="input-group-append">
        <button class="btn btn-outline-secondary" type="button" id="inputGroupFileAddon04">Button</button>
      </div>
    </div>

## Accessibility

Ensure that all form controls have an appropriate accessible name so that their purpose can be conveyed to users of assistive technologies. The simplest way to achieve this is to use a `<label>` element, or—in the case of buttons—to include sufficiently descriptive text as part of the `<button>...</button>` content.

For situations where it’s not possible to include a visible `<label>` or appropriate text content, there are alternative ways of still providing an accessible name, such as:

  * `<label>` elements hidden using the `.visually-hidden` class
  * Pointing to an existing element that can act as a label using `aria-labelledby`
  * Providing a `title` attribute
  * Explicitly setting the accessible name on an element using `aria-label`



If none of these are present, assistive technologies may resort to using the `placeholder` attribute as a fallback for the accessible name on `<input>` and `<textarea>` elements. The examples in this section provide a few suggested, case-specific approaches.

While using visually hidden content (`.sr-only`, `aria-label`, and even `placeholder` content, which disappears once a form field has content) will benefit assistive technology users, a lack of visible label text may still be problematic for certain users. Some form of visible label is generally the best approach, both for accessibility and usability.
  *[WCAG]: Web Content Accessibility Guidelines
  *[WAI]: Web Accessibility Initiative
  *[ARIA]: Accessible Rich Internet Applications
  *[attr]: attribute
  *[HTML]: HyperText Markup Language
  *[RFS]: Responsive font sizes
