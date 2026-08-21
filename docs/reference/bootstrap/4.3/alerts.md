> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente: https://getbootstrap.com/docs/4.3/components/alerts/
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



  * Examples
    * Link color
    * Additional content
    * Dismissing
  * JavaScript behavior
    * Triggers
    * Methods
    * Events



# Alerts

Provide contextual feedback messages for typical user actions with the handful of available and flexible alert messages.

## Examples

Alerts are available for any length of text, as well as an optional dismiss button. For proper styling, use one of the eight **required** contextual classes (e.g., `.alert-success`). For inline dismissal, use the alerts jQuery plugin.

A simple primary alert—check it out! 

A simple secondary alert—check it out! 

A simple success alert—check it out! 

A simple danger alert—check it out! 

A simple warning alert—check it out! 

A simple info alert—check it out! 

A simple light alert—check it out! 

A simple dark alert—check it out! 
    
    
    <div class="alert alert-primary" role="alert">
      A simple primary alert—check it out!
    </div>
    <div class="alert alert-secondary" role="alert">
      A simple secondary alert—check it out!
    </div>
    <div class="alert alert-success" role="alert">
      A simple success alert—check it out!
    </div>
    <div class="alert alert-danger" role="alert">
      A simple danger alert—check it out!
    </div>
    <div class="alert alert-warning" role="alert">
      A simple warning alert—check it out!
    </div>
    <div class="alert alert-info" role="alert">
      A simple info alert—check it out!
    </div>
    <div class="alert alert-light" role="alert">
      A simple light alert—check it out!
    </div>
    <div class="alert alert-dark" role="alert">
      A simple dark alert—check it out!
    </div>

##### Conveying meaning to assistive technologies

Using color to add meaning only provides a visual indication, which will not be conveyed to users of assistive technologies – such as screen readers. Ensure that information denoted by the color is either obvious from the content itself (e.g. the visible text), or is included through alternative means, such as additional text hidden with the `.sr-only` class.

### Link color

Use the `.alert-link` utility class to quickly provide matching colored links within any alert.

A simple primary alert with an example link. Give it a click if you like. 

A simple secondary alert with an example link. Give it a click if you like. 

A simple success alert with an example link. Give it a click if you like. 

A simple danger alert with an example link. Give it a click if you like. 

A simple warning alert with an example link. Give it a click if you like. 

A simple info alert with an example link. Give it a click if you like. 

A simple light alert with an example link. Give it a click if you like. 

A simple dark alert with an example link. Give it a click if you like. 
    
    
    <div class="alert alert-primary" role="alert">
      A simple primary alert with <a href="#" class="alert-link">an example link</a>. Give it a click if you like.
    </div>
    <div class="alert alert-secondary" role="alert">
      A simple secondary alert with <a href="#" class="alert-link">an example link</a>. Give it a click if you like.
    </div>
    <div class="alert alert-success" role="alert">
      A simple success alert with <a href="#" class="alert-link">an example link</a>. Give it a click if you like.
    </div>
    <div class="alert alert-danger" role="alert">
      A simple danger alert with <a href="#" class="alert-link">an example link</a>. Give it a click if you like.
    </div>
    <div class="alert alert-warning" role="alert">
      A simple warning alert with <a href="#" class="alert-link">an example link</a>. Give it a click if you like.
    </div>
    <div class="alert alert-info" role="alert">
      A simple info alert with <a href="#" class="alert-link">an example link</a>. Give it a click if you like.
    </div>
    <div class="alert alert-light" role="alert">
      A simple light alert with <a href="#" class="alert-link">an example link</a>. Give it a click if you like.
    </div>
    <div class="alert alert-dark" role="alert">
      A simple dark alert with <a href="#" class="alert-link">an example link</a>. Give it a click if you like.
    </div>

### Additional content

Alerts can also contain additional HTML elements like headings, paragraphs and dividers.

#### Well done!

Aww yeah, you successfully read this important alert message. This example text is going to run a bit longer so that you can see how spacing within an alert works with this kind of content.

* * *

Whenever you need to, be sure to use margin utilities to keep things nice and tidy.
    
    
    <div class="alert alert-success" role="alert">
      <h4 class="alert-heading">Well done!</h4>
      <p>Aww yeah, you successfully read this important alert message. This example text is going to run a bit longer so that you can see how spacing within an alert works with this kind of content.</p>
      <hr>
      <p class="mb-0">Whenever you need to, be sure to use margin utilities to keep things nice and tidy.</p>
    </div>

### Dismissing

Using the alert JavaScript plugin, it’s possible to dismiss any alert inline. Here’s how:

  * Be sure you’ve loaded the alert plugin, or the compiled Bootstrap JavaScript.
  * If you’re building our JavaScript from source, it [requires `util.js`](/docs/4.3/getting-started/javascript/#util). The compiled version includes this.
  * Add a dismiss button and the `.alert-dismissible` class, which adds extra padding to the right of the alert and positions the `.close` button.
  * On the dismiss button, add the `data-dismiss="alert"` attribute, which triggers the JavaScript functionality. Be sure to use the `<button>` element with it for proper behavior across all devices.
  * To animate alerts when dismissing them, be sure to add the `.fade` and `.show` classes.



You can see this in action with a live demo:

**Holy guacamole!** You should check in on some of those fields below.  ×
    
    
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <strong>Holy guacamole!</strong> You should check in on some of those fields below.
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>

## JavaScript behavior

### Triggers

Enable dismissal of an alert via JavaScript:
    
    
    $('.alert').alert()

Or with `data` attributes on a button **within the alert** , as demonstrated above:
    
    
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>

Note that closing an alert will remove it from the DOM.

### Methods

Method | Description  
---|---  
`$().alert()` | Makes an alert listen for click events on descendant elements which have the `data-dismiss="alert"` attribute. (Not necessary when using the data-api’s auto-initialization.)  
`$().alert('close')` | Closes an alert by removing it from the DOM. If the `.fade` and `.show` classes are present on the element, the alert will fade out before it is removed.  
`$().alert('dispose')` | Destroys an element’s alert.  
      
    
    $('.alert').alert('close')

### Events

Bootstrap’s alert plugin exposes a few events for hooking into alert functionality.

Event | Description  
---|---  
`close.bs.alert` | This event fires immediately when the `close` instance method is called.  
`closed.bs.alert` | This event is fired when the alert has been closed (will wait for CSS transitions to complete).  
      
    
    $('#myAlert').on('closed.bs.alert', function () {
      // do something...
    })
  *[WCAG]: Web Content Accessibility Guidelines
  *[WAI]: Web Accessibility Initiative
  *[ARIA]: Accessible Rich Internet Applications
