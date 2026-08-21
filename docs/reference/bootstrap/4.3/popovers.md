> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente: https://getbootstrap.com/docs/4.3/components/popovers/
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



  * Overview
  * Example: Enable popovers everywhere
  * Example: Using the container option
  * Example
    * Four directions
    * Dismiss on next click
    * Disabled elements
  * Usage
    * Options
    * Methods
      * $().popover(options)
      * .popover('show')
      * .popover('hide')
      * .popover('toggle')
      * .popover('dispose')
      * .popover('enable')
      * .popover('disable')
      * .popover('toggleEnabled')
      * .popover('update')
    * Events



# Popovers

Documentation and examples for adding Bootstrap popovers, like those found in iOS, to any element on your site.

## Overview

Things to know when using the popover plugin:

  * Popovers rely on the 3rd party library [Popper.js](https://popper.js.org/) for positioning. You must include [popper.min.js](https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js) before bootstrap.js or use `bootstrap.bundle.min.js` / `bootstrap.bundle.js` which contains Popper.js in order for popovers to work!
  * Popovers require the [tooltip plugin](/docs/4.3/components/tooltips/) as a dependency.
  * If you’re building our JavaScript from source, it [requires `util.js`](/docs/4.3/getting-started/javascript/#util).
  * Popovers are opt-in for performance reasons, so **you must initialize them yourself**.
  * Zero-length `title` and `content` values will never show a popover.
  * Specify `container: 'body'` to avoid rendering problems in more complex components (like our input groups, button groups, etc).
  * Triggering popovers on hidden elements will not work.
  * Popovers for `.disabled` or `disabled` elements must be triggered on a wrapper element.
  * When triggered from anchors that wrap across multiple lines, popovers will be centered between the anchors’ overall width. Use `.text-nowrap` on your `<a>`s to avoid this behavior.
  * Popovers must be hidden before their corresponding elements have been removed from the DOM.
  * Popovers can be triggered thanks to an element inside a shadow DOM.



The animation effect of this component is dependent on the `prefers-reduced-motion` media query. See the [reduced motion section of our accessibility documentation](/docs/4.3/getting-started/accessibility/#reduced-motion).

Keep reading to see how popovers work with some examples.

## Example: Enable popovers everywhere

One way to initialize all popovers on a page would be to select them by their `data-toggle` attribute:
    
    
    $(function () {
      $('[data-toggle="popover"]').popover()
    })

## Example: Using the `container` option

When you have some styles on a parent element that interfere with a popover, you’ll want to specify a custom `container` so that the popover’s HTML appears within that element instead.
    
    
    $(function () {
      $('.example-popover').popover({
        container: 'body'
      })
    })

## Example

Click to toggle popover
    
    
    <button type="button" class="btn btn-lg btn-danger" data-toggle="popover" title="Popover title" data-content="And here's some amazing content. It's very engaging. Right?">Click to toggle popover</button>

### Four directions

Four options are available: top, right, bottom, and left aligned.

Popover on top  Popover on right  Popover on bottom  Popover on left 
    
    
    <button type="button" class="btn btn-secondary" data-container="body" data-toggle="popover" data-placement="top" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">
      Popover on top
    </button>
    
    <button type="button" class="btn btn-secondary" data-container="body" data-toggle="popover" data-placement="right" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">
      Popover on right
    </button>
    
    <button type="button" class="btn btn-secondary" data-container="body" data-toggle="popover" data-placement="bottom" data-content="Vivamus
    sagittis lacus vel augue laoreet rutrum faucibus.">
      Popover on bottom
    </button>
    
    <button type="button" class="btn btn-secondary" data-container="body" data-toggle="popover" data-placement="left" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">
      Popover on left
    </button>

### Dismiss on next click

Use the `focus` trigger to dismiss popovers on the user’s next click of a different element than the toggle element.

#### Specific markup required for dismiss-on-next-click

For proper cross-browser and cross-platform behavior, you must use the `<a>` tag, _not_ the `<button>` tag, and you also must include a [`tabindex`](https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/tabindex) attribute.

Dismissible popover
    
    
    <a tabindex="0" class="btn btn-lg btn-danger" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a>
    
    
    $('.popover-dismiss').popover({
      trigger: 'focus'
    })

### Disabled elements

Elements with the `disabled` attribute aren’t interactive, meaning users cannot hover or click them to trigger a popover (or tooltip). As a workaround, you’ll want to trigger the popover from a wrapper `<div>` or `<span>` and override the `pointer-events` on the disabled element.

For disabled popover triggers, you may also prefer `data-trigger="hover"` so that the popover appears as immediate visual feedback to your users as they may not expect to _click_ on a disabled element.

Disabled button
    
    
    <span class="d-inline-block" data-toggle="popover" data-content="Disabled popover">
      <button class="btn btn-primary" style="pointer-events: none;" type="button" disabled>Disabled button</button>
    </span>

## Usage

Enable popovers via JavaScript:
    
    
    $('#example').popover(options)

### Options

Options can be passed via data attributes or JavaScript. For data attributes, append the option name to `data-`, as in `data-animation=""`.

Note that for security reasons the `sanitize`, `sanitizeFn` and `whiteList` options cannot be supplied using data attributes.

Name | Type | Default | Description  
---|---|---|---  
animation | boolean | true | Apply a CSS fade transition to the popover  
container | string | element | false | false |  Appends the popover to a specific element. Example: `container: 'body'`. This option is particularly useful in that it allows you to position the popover in the flow of the document near the triggering element - which will prevent the popover from floating away from the triggering element during a window resize.  
content | string | element | function | '' |  Default content value if `data-content` attribute isn't present. If a function is given, it will be called with its `this` reference set to the element that the popover is attached to.  
delay | number | object | 0 |  Delay showing and hiding the popover (ms) - does not apply to manual trigger type If a number is supplied, delay is applied to both hide/show Object structure is: `delay: { "show": 500, "hide": 100 }`  
html | boolean | false | Insert HTML into the popover. If false, jQuery's `text` method will be used to insert content into the DOM. Use text if you're worried about XSS attacks.  
placement | string | function | 'right' |  How to position the popover - auto | top | bottom | left | right.  
When `auto` is specified, it will dynamically reorient the popover. When a function is used to determine the placement, it is called with the popover DOM node as its first argument and the triggering element DOM node as its second. The `this` context is set to the popover instance.  
selector | string | false | false | If a selector is provided, popover objects will be delegated to the specified targets. In practice, this is used to enable dynamic HTML content to have popovers added. See [this](https://github.com/twbs/bootstrap/issues/4215) and [an informative example](https://codepen.io/Johann-S/pen/djJYPb).  
template | string | `'<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>'` |  Base HTML to use when creating the popover. The popover's `title` will be injected into the `.popover-header`. The popover's `content` will be injected into the `.popover-body`. `.arrow` will become the popover's arrow. The outermost wrapper element should have the `.popover` class.  
title | string | element | function | '' |  Default title value if `title` attribute isn't present. If a function is given, it will be called with its `this` reference set to the element that the popover is attached to.  
trigger | string | 'click' | How popover is triggered - click | hover | focus | manual. You may pass multiple triggers; separate them with a space. `manual` cannot be combined with any other trigger.  
offset | number | string | 0 | Offset of the popover relative to its target. For more information refer to Popper.js's [offset docs](https://popper.js.org/popper-documentation.html#modifiers..offset.offset).  
fallbackPlacement | string | array | 'flip' | Allow to specify which position Popper will use on fallback. For more information refer to Popper.js's [behavior docs](https://popper.js.org/popper-documentation.html#modifiers..flip.behavior)  
boundary | string | element | 'scrollParent' | Overflow constraint boundary of the popover. Accepts the values of `'viewport'`, `'window'`, `'scrollParent'`, or an HTMLElement reference (JavaScript only). For more information refer to Popper.js's [preventOverflow docs](https://popper.js.org/popper-documentation.html#modifiers..preventOverflow.boundariesElement).  
sanitize | boolean | true | Enable or disable the sanitization. If activated `'template'`, `'content'` and `'title'` options will be sanitized.  
whiteList | object | [Default value](/docs/4.3/getting-started/javascript/#sanitizer) | Object which contains allowed attributes and tags  
sanitizeFn | null | function | null | Here you can supply your own sanitize function. This can be useful if you prefer to use a dedicated library to perform sanitization.  
  
#### Data attributes for individual popovers

Options for individual popovers can alternatively be specified through the use of data attributes, as explained above.

### Methods

#### Asynchronous methods and transitions

All API methods are **asynchronous** and start a **transition**. They return to the caller as soon as the transition is started but **before it ends**. In addition, a method call on a **transitioning component will be ignored**.

[See our JavaScript documentation for more information](/docs/4.3/getting-started/javascript/).

#### `$().popover(options)`

Initializes popovers for an element collection.

#### `.popover('show')`

Reveals an element’s popover. **Returns to the caller before the popover has actually been shown** (i.e. before the `shown.bs.popover` event occurs). This is considered a “manual” triggering of the popover. Popovers whose both title and content are zero-length are never displayed.
    
    
    $('#element').popover('show')

#### `.popover('hide')`

Hides an element’s popover. **Returns to the caller before the popover has actually been hidden** (i.e. before the `hidden.bs.popover` event occurs). This is considered a “manual” triggering of the popover.
    
    
    $('#element').popover('hide')

#### `.popover('toggle')`

Toggles an element’s popover. **Returns to the caller before the popover has actually been shown or hidden** (i.e. before the `shown.bs.popover` or `hidden.bs.popover` event occurs). This is considered a “manual” triggering of the popover.
    
    
    $('#element').popover('toggle')

#### `.popover('dispose')`

Hides and destroys an element’s popover. Popovers that use delegation (which are created using the `selector` option) cannot be individually destroyed on descendant trigger elements.
    
    
    $('#element').popover('dispose')

#### `.popover('enable')`

Gives an element’s popover the ability to be shown. **Popovers are enabled by default.**
    
    
    $('#element').popover('enable')

#### `.popover('disable')`

Removes the ability for an element’s popover to be shown. The popover will only be able to be shown if it is re-enabled.
    
    
    $('#element').popover('disable')

#### `.popover('toggleEnabled')`

Toggles the ability for an element’s popover to be shown or hidden.
    
    
    $('#element').popover('toggleEnabled')

#### `.popover('update')`

Updates the position of an element’s popover.
    
    
    $('#element').popover('update')

### Events

Event Type | Description  
---|---  
show.bs.popover | This event fires immediately when the `show` instance method is called.  
shown.bs.popover | This event is fired when the popover has been made visible to the user (will wait for CSS transitions to complete).  
hide.bs.popover | This event is fired immediately when the `hide` instance method has been called.  
hidden.bs.popover | This event is fired when the popover has finished being hidden from the user (will wait for CSS transitions to complete).  
inserted.bs.popover | This event is fired after the `show.bs.popover` event when the popover template has been added to the DOM.  
      
    
    $('#myPopover').on('hidden.bs.popover', function () {
      // do something...
    })
