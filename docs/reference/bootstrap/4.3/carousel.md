> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente: https://getbootstrap.com/docs/4.3/components/carousel/
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



  * How it works
  * Example
    * Slides only
    * With controls
    * With indicators
    * With captions
    * Crossfade
    * Individual .carousel-item interval
  * Usage
    * Via data attributes
    * Via JavaScript
    * Options
    * Methods
      * .carousel(options)
      * .carousel('cycle')
      * .carousel('pause')
      * .carousel(number)
      * .carousel('prev')
      * .carousel('next')
      * .carousel('dispose')
    * Events
    * Change transition duration



# Carousel

A slideshow component for cycling through elements—images or slides of text—like a carousel.

## How it works

The carousel is a slideshow for cycling through a series of content, built with CSS 3D transforms and a bit of JavaScript. It works with a series of images, text, or custom markup. It also includes support for previous/next controls and indicators.

In browsers where the [Page Visibility API](https://www.w3.org/TR/page-visibility/) is supported, the carousel will avoid sliding when the webpage is not visible to the user (such as when the browser tab is inactive, the browser window is minimized, etc.).

The animation effect of this component is dependent on the `prefers-reduced-motion` media query. See the [reduced motion section of our accessibility documentation](/docs/4.3/getting-started/accessibility/#reduced-motion).

Please be aware that nested carousels are not supported, and carousels are generally not compliant with accessibility standards.

Lastly, if you’re building our JavaScript from source, it [requires `util.js`](/docs/4.3/getting-started/javascript/#util).

## Example

Carousels don’t automatically normalize slide dimensions. As such, you may need to use additional utilities or custom styles to appropriately size content. While carousels support previous/next controls and indicators, they’re not explicitly required. Add and customize as you see fit.

**The`.active` class needs to be added to one of the slides** otherwise the carousel will not be visible. Also be sure to set a unique id on the `.carousel` for optional controls, especially if you’re using multiple carousels on a single page. Control and indicator elements must have a `data-target` attribute (or `href` for links) that matches the id of the `.carousel` element.

### Slides only

Here’s a carousel with slides only. Note the presence of the `.d-block` and `.w-100` on carousel images to prevent browser default image alignment.

PlaceholderFirst slide

PlaceholderSecond slide

PlaceholderThird slide
    
    
    <div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="..." class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="..." class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="..." class="d-block w-100" alt="...">
        </div>
      </div>
    </div>

### With controls

Adding in the previous and next controls:

PlaceholderFirst slide

PlaceholderSecond slide

PlaceholderThird slide

Previous Next
    
    
    <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="..." class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="..." class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="..." class="d-block w-100" alt="...">
        </div>
      </div>
      <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>

### With indicators

You can also add the indicators to the carousel, alongside the controls, too.

  1.   2.   3. 


PlaceholderFirst slide

PlaceholderSecond slide

PlaceholderThird slide

Previous Next
    
    
    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
      <ol class="carousel-indicators">
        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
      </ol>
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="..." class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="..." class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="..." class="d-block w-100" alt="...">
        </div>
      </div>
      <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>

### With captions

Add captions to your slides easily with the `.carousel-caption` element within any `.carousel-item`. They can be easily hidden on smaller viewports, as shown below, with optional [display utilities](/docs/4.3/utilities/display/). We hide them initially with `.d-none` and bring them back on medium-sized devices with `.d-md-block`.

  1.   2.   3. 


PlaceholderFirst slide

##### First slide label

Nulla vitae elit libero, a pharetra augue mollis interdum.

PlaceholderSecond slide

##### Second slide label

Lorem ipsum dolor sit amet, consectetur adipiscing elit.

PlaceholderThird slide

##### Third slide label

Praesent commodo cursus magna, vel scelerisque nisl consectetur.

Previous Next
    
    
    <div class="bd-example">
      <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
          <li data-target="#carouselExampleCaptions" data-slide-to="0" class="active"></li>
          <li data-target="#carouselExampleCaptions" data-slide-to="1"></li>
          <li data-target="#carouselExampleCaptions" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
          <div class="carousel-item active">
            <img src="..." class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block">
              <h5>First slide label</h5>
              <p>Nulla vitae elit libero, a pharetra augue mollis interdum.</p>
            </div>
          </div>
          <div class="carousel-item">
            <img src="..." class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block">
              <h5>Second slide label</h5>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            </div>
          </div>
          <div class="carousel-item">
            <img src="..." class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block">
              <h5>Third slide label</h5>
              <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur.</p>
            </div>
          </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="sr-only">Next</span>
        </a>
      </div>
    </div>

### Crossfade

Add `.carousel-fade` to your carousel to animate slides with a fade transition instead of a slide.

PlaceholderFirst slide

PlaceholderSecond slide

PlaceholderThird slide

Previous Next
    
    
    <div id="carouselExampleFade" class="carousel slide carousel-fade" data-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="..." class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="..." class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="..." class="d-block w-100" alt="...">
        </div>
      </div>
      <a class="carousel-control-prev" href="#carouselExampleFade" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#carouselExampleFade" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>

### Individual `.carousel-item` interval

Add `data-interval=""` to a `.carousel-item` to change the amount of time to delay between automatically cycling to the next item.

PlaceholderFirst slide

PlaceholderSecond slide

PlaceholderThird slide

Previous Next
    
    
    <div id="carouselExampleInterval" class="carousel slide" data-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active" data-interval="10000">
          <img src="..." class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item" data-interval="2000">
          <img src="..." class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="..." class="d-block w-100" alt="...">
        </div>
      </div>
      <a class="carousel-control-prev" href="#carouselExampleInterval" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#carouselExampleInterval" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>

## Usage

### Via data attributes

Use data attributes to easily control the position of the carousel. `data-slide` accepts the keywords `prev` or `next`, which alters the slide position relative to its current position. Alternatively, use `data-slide-to` to pass a raw slide index to the carousel `data-slide-to="2"`, which shifts the slide position to a particular index beginning with `0`.

The `data-ride="carousel"` attribute is used to mark a carousel as animating starting at page load. If you don’t use `data-ride="carousel"` to initialize your carousel, you have to initialize it yourself. **It cannot be used in combination with (redundant and unnecessary) explicit JavaScript initialization of the same carousel.**

### Via JavaScript

Call carousel manually with:
    
    
    $('.carousel').carousel()

### Options

Options can be passed via data attributes or JavaScript. For data attributes, append the option name to `data-`, as in `data-interval=""`.

Name | Type | Default | Description  
---|---|---|---  
interval | number | 5000 | The amount of time to delay between automatically cycling an item. If false, carousel will not automatically cycle.  
keyboard | boolean | true | Whether the carousel should react to keyboard events.  
pause | string | boolean | "hover" | If set to `"hover"`, pauses the cycling of the carousel on `mouseenter` and resumes the cycling of the carousel on `mouseleave`. If set to `false`, hovering over the carousel won't pause it. On touch-enabled devices, when set to `"hover"`, cycling will pause on `touchend` (once the user finished interacting with the carousel) for two intervals, before automatically resuming. Note that this is in addition to the above mouse behavior.  
ride | string | false | Autoplays the carousel after the user manually cycles the first item. If "carousel", autoplays the carousel on load.  
wrap | boolean | true | Whether the carousel should cycle continuously or have hard stops.  
touch | boolean | true | Whether the carousel should support left/right swipe interactions on touchscreen devices.  
  
### Methods

#### Asynchronous methods and transitions

All API methods are **asynchronous** and start a **transition**. They return to the caller as soon as the transition is started but **before it ends**. In addition, a method call on a **transitioning component will be ignored**.

[See our JavaScript documentation for more information](/docs/4.3/getting-started/javascript/).

#### `.carousel(options)`

Initializes the carousel with an optional options `object` and starts cycling through items.
    
    
    $('.carousel').carousel({
      interval: 2000
    })

#### `.carousel('cycle')`

Cycles through the carousel items from left to right.

#### `.carousel('pause')`

Stops the carousel from cycling through items.

#### `.carousel(number)`

Cycles the carousel to a particular frame (0 based, similar to an array). **Returns to the caller before the target item has been shown** (i.e. before the `slid.bs.carousel` event occurs).

#### `.carousel('prev')`

Cycles to the previous item. **Returns to the caller before the previous item has been shown** (i.e. before the `slid.bs.carousel` event occurs).

#### `.carousel('next')`

Cycles to the next item. **Returns to the caller before the next item has been shown** (i.e. before the `slid.bs.carousel` event occurs).

#### `.carousel('dispose')`

Destroys an element’s carousel.

### Events

Bootstrap’s carousel class exposes two events for hooking into carousel functionality. Both events have the following additional properties:

  * `direction`: The direction in which the carousel is sliding (either `"left"` or `"right"`).
  * `relatedTarget`: The DOM element that is being slid into place as the active item.
  * `from`: The index of the current item
  * `to`: The index of the next item



All carousel events are fired at the carousel itself (i.e. at the `<div class="carousel">`).

Event Type | Description  
---|---  
slide.bs.carousel | This event fires immediately when the `slide` instance method is invoked.  
slid.bs.carousel | This event is fired when the carousel has completed its slide transition.  
      
    
    $('#myCarousel').on('slide.bs.carousel', function () {
      // do something...
    })

### Change transition duration

The transition duration of `.carousel-item` can be changed with the `$carousel-transition` Sass variable before compiling or custom styles if you’re using the compiled CSS. If multiple transitions are applied, make sure the transform transition is defined first (eg. `transition: transform 2s ease, opacity .5s ease-out`).
  *[WCAG]: Web Content Accessibility Guidelines
  *[WAI]: Web Accessibility Initiative
  *[ARIA]: Accessible Rich Internet Applications
