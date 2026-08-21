> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente: https://getbootstrap.com/docs/4.3/components/input-group/
> Capturado: 2026-08-21

Skip to main content

[There's a newer version of Bootstrap!](https://getbootstrap.com/)

[Bootstrap](/)

  * [Home](/)
  * [Documentation](/docs/4.3/getting-started/introduction/)
  * [Examples](/docs/4.3/examples/)
  * [Expo](https://expo.getbootstrap.com/)
  * [Blog](https://blog.getbootstrap.com/)



  * v4.3 

[Latest (4.3.x)](/docs/4.3/) [v4.2.1](https://getbootstrap.com/docs/4.2/) [v4.0.0](https://getbootstrap.com/docs/4.0/)

[v4 Alpha 6](https://v4-alpha.getbootstrap.com/) [v3.4.1](https://getbootstrap.com/docs/3.4/) [v3.3.7](https://getbootstrap.com/docs/3.3/) [v2.3.2](https://getbootstrap.com/2.3.2/)

[All versions](/docs/versions/)

  * [GitHub](https://github.com/twbs/bootstrap)
  * [Twitter](https://twitter.com/getbootstrap)
  * [Open Collective](https://opencollective.com/bootstrap/)

[Download](/docs/4.3/getting-started/download/)

Menu

[ Getting started ](/docs/4.3/getting-started/introduction/)

  * [ Introduction ](/docs/4.3/getting-started/introduction/)
  * [ Download ](/docs/4.3/getting-started/download/)
  * [ Contents ](/docs/4.3/getting-started/contents/)
  * [ Browsers & devices ](/docs/4.3/getting-started/browsers-devices/)
  * [ JavaScript ](/docs/4.3/getting-started/javascript/)
  * [ Theming ](/docs/4.3/getting-started/theming/)
  * [ Build tools ](/docs/4.3/getting-started/build-tools/)
  * [ Webpack ](/docs/4.3/getting-started/webpack/)
  * [ Accessibility ](/docs/4.3/getting-started/accessibility/)



[ Layout ](/docs/4.3/layout/overview/)

  * [ Overview ](/docs/4.3/layout/overview/)
  * [ Grid ](/docs/4.3/layout/grid/)
  * [ Utilities for layout ](/docs/4.3/layout/utilities-for-layout/)



[ Content ](/docs/4.3/content/reboot/)

  * [ Reboot ](/docs/4.3/content/reboot/)
  * [ Typography ](/docs/4.3/content/typography/)
  * [ Code ](/docs/4.3/content/code/)
  * [ Images ](/docs/4.3/content/images/)
  * [ Tables ](/docs/4.3/content/tables/)
  * [ Figures ](/docs/4.3/content/figures/)



[ Components ](/docs/4.3/components/alerts/)

  * [ Alerts ](/docs/4.3/components/alerts/)
  * [ Badge ](/docs/4.3/components/badge/)
  * [ Breadcrumb ](/docs/4.3/components/breadcrumb/)
  * [ Buttons ](/docs/4.3/components/buttons/)
  * [ Button group ](/docs/4.3/components/button-group/)
  * [ Card ](/docs/4.3/components/card/)
  * [ Carousel ](/docs/4.3/components/carousel/)
  * [ Collapse ](/docs/4.3/components/collapse/)
  * [ Dropdowns ](/docs/4.3/components/dropdowns/)
  * [ Forms ](/docs/4.3/components/forms/)
  * [ Input group ](/docs/4.3/components/input-group/)
  * [ Jumbotron ](/docs/4.3/components/jumbotron/)
  * [ List group ](/docs/4.3/components/list-group/)
  * [ Media object ](/docs/4.3/components/media-object/)
  * [ Modal ](/docs/4.3/components/modal/)
  * [ Navs ](/docs/4.3/components/navs/)
  * [ Navbar ](/docs/4.3/components/navbar/)
  * [ Pagination ](/docs/4.3/components/pagination/)
  * [ Popovers ](/docs/4.3/components/popovers/)
  * [ Progress ](/docs/4.3/components/progress/)
  * [ Scrollspy ](/docs/4.3/components/scrollspy/)
  * [ Spinners ](/docs/4.3/components/spinners/)
  * [ Toasts ](/docs/4.3/components/toasts/)
  * [ Tooltips ](/docs/4.3/components/tooltips/)



[ Utilities ](/docs/4.3/utilities/borders/)

  * [ Borders ](/docs/4.3/utilities/borders/)
  * [ Clearfix ](/docs/4.3/utilities/clearfix/)
  * [ Close icon ](/docs/4.3/utilities/close-icon/)
  * [ Colors ](/docs/4.3/utilities/colors/)
  * [ Display ](/docs/4.3/utilities/display/)
  * [ Embed ](/docs/4.3/utilities/embed/)
  * [ Flex ](/docs/4.3/utilities/flex/)
  * [ Float ](/docs/4.3/utilities/float/)
  * [ Image replacement ](/docs/4.3/utilities/image-replacement/)
  * [ Overflow ](/docs/4.3/utilities/overflow/)
  * [ Position ](/docs/4.3/utilities/position/)
  * [ Screen readers ](/docs/4.3/utilities/screen-readers/)
  * [ Shadows ](/docs/4.3/utilities/shadows/)
  * [ Sizing ](/docs/4.3/utilities/sizing/)
  * [ Spacing ](/docs/4.3/utilities/spacing/)
  * [ Stretched link ](/docs/4.3/utilities/stretched-link/)
  * [ Text ](/docs/4.3/utilities/text/)
  * [ Vertical align ](/docs/4.3/utilities/vertical-align/)
  * [ Visibility ](/docs/4.3/utilities/visibility/)



[ Extend ](/docs/4.3/extend/approach/)

  * [ Approach ](/docs/4.3/extend/approach/)
  * [ Icons ](/docs/4.3/extend/icons/)



[ Migration ](/docs/4.3/migration/)




[ About ](/docs/4.3/about/overview/)

  * [ Overview ](/docs/4.3/about/overview/)
  * [ Team ](/docs/4.3/about/team/)
  * [ Brand ](/docs/4.3/about/brand/)
  * [ License ](/docs/4.3/about/license/)
  * [ Translations ](/docs/4.3/about/translations/)



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

Screen readers will have trouble with your forms if you don’t include a label for every input. For these input groups, ensure that any additional label or functionality is conveyed to assistive technologies.

The exact technique to be used (`<label>` elements hidden using the `.sr-only` class, or use of the `aria-label` and `aria-labelledby` attributes, possibly in combination with `aria-describedby`) and what additional information will need to be conveyed will vary depending on the exact type of interface widget you’re implementing. The examples in this section provide a few suggested, case-specific approaches.
  *[WCAG]: Web Content Accessibility Guidelines
  *[WAI]: Web Accessibility Initiative
  *[ARIA]: Accessible Rich Internet Applications
