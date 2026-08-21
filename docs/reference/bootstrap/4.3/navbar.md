> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente: https://getbootstrap.com/docs/4.3/components/navbar/
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
  * Supported content
    * Brand
    * Nav
    * Forms
    * Text
  * Color schemes
  * Containers
  * Placement
  * Responsive behaviors
    * Toggler
    * External content



# Navbar

Documentation and examples for Bootstrap’s powerful, responsive navigation header, the navbar. Includes support for branding, navigation, and more, including support for our collapse plugin.

## How it works

Here’s what you need to know before getting started with the navbar:

  * Navbars require a wrapping `.navbar` with `.navbar-expand{-sm|-md|-lg|-xl}` for responsive collapsing and color scheme classes.
  * Navbars and their contents are fluid by default. Use optional containers to limit their horizontal width.
  * Use our [spacing](/docs/4.3/utilities/spacing/) and [flex](/docs/4.3/utilities/flex/) utility classes for controlling spacing and alignment within navbars.
  * Navbars are responsive by default, but you can easily modify them to change that. Responsive behavior depends on our Collapse JavaScript plugin.
  * Navbars are hidden by default when printing. Force them to be printed by adding `.d-print` to the `.navbar`. See the [display](/docs/4.3/utilities/display/) utility class.
  * Ensure accessibility by using a `<nav>` element or, if using a more generic element such as a `<div>`, add a `role="navigation"` to every navbar to explicitly identify it as a landmark region for users of assistive technologies.



The animation effect of this component is dependent on the `prefers-reduced-motion` media query. See the [reduced motion section of our accessibility documentation](/docs/4.3/getting-started/accessibility/#reduced-motion).

Read on for an example and list of supported sub-components.

## Supported content

Navbars come with built-in support for a handful of sub-components. Choose from the following as needed:

  * `.navbar-brand` for your company, product, or project name.
  * `.navbar-nav` for a full-height and lightweight navigation (including support for dropdowns).
  * `.navbar-toggler` for use with our collapse plugin and other navigation toggling behaviors.
  * `.form-inline` for any form controls and actions.
  * `.navbar-text` for adding vertically centered strings of text.
  * `.collapse.navbar-collapse` for grouping and hiding navbar contents by a parent breakpoint.



Here’s an example of all the sub-components included in a responsive light-themed navbar that automatically collapses at the `lg` (large) breakpoint.

Navbar

  * Home (current)
  * Link
  * Dropdown 

Action Another action

Something else here

  * Disabled

Search
    
    
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="#">Navbar</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Dropdown
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="#">Action</a>
              <a class="dropdown-item" href="#">Another action</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#">Something else here</a>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
          </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
          <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
      </div>
    </nav>

This example uses [color](/docs/4.3/utilities/colors/) (`bg-light`) and [spacing](/docs/4.3/utilities/spacing/) (`my-2`, `my-lg-0`, `mr-sm-0`, `my-sm-0`) utility classes.

### Brand

The `.navbar-brand` can be applied to most elements, but an anchor works best as some elements might require utility classes or custom styles.

Navbar Navbar
    
    
    <!-- As a link -->
    <nav class="navbar navbar-light bg-light">
      <a class="navbar-brand" href="#">Navbar</a>
    </nav>
    
    <!-- As a heading -->
    <nav class="navbar navbar-light bg-light">
      <span class="navbar-brand mb-0 h1">Navbar</span>
    </nav>

Adding images to the `.navbar-brand` will likely always require custom styles or utilities to properly size. Here are some examples to demonstrate.
    
    
    <!-- Just an image -->
    <nav class="navbar navbar-light bg-light">
      <a class="navbar-brand" href="#">
        <img src="/docs/4.3/assets/brand/bootstrap-solid.svg" width="30" height="30" alt="">
      </a>
    </nav>

Bootstrap 
    
    
    <!-- Image and text -->
    <nav class="navbar navbar-light bg-light">
      <a class="navbar-brand" href="#">
        <img src="/docs/4.3/assets/brand/bootstrap-solid.svg" width="30" height="30" class="d-inline-block align-top" alt="">
        Bootstrap
      </a>
    </nav>

### Nav

Navbar navigation links build on our `.nav` options with their own modifier class and require the use of toggler classes for proper responsive styling. **Navigation in navbars will also grow to occupy as much horizontal space as possible** to keep your navbar contents securely aligned.

Active states—with `.active`—to indicate the current page can be applied directly to `.nav-link`s or their immediate parent `.nav-item`s.

Navbar

  * Home (current)
  * Features
  * Pricing
  * Disabled


    
    
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="#">Navbar</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Features</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Pricing</a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
          </li>
        </ul>
      </div>
    </nav>

And because we use classes for our navs, you can avoid the list-based approach entirely if you like.

Navbar

Home (current) Features Pricing Disabled
    
    
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="#">Navbar</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
          <a class="nav-item nav-link active" href="#">Home <span class="sr-only">(current)</span></a>
          <a class="nav-item nav-link" href="#">Features</a>
          <a class="nav-item nav-link" href="#">Pricing</a>
          <a class="nav-item nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
        </div>
      </div>
    </nav>

You may also utilize dropdowns in your navbar nav. Dropdown menus require a wrapping element for positioning, so be sure to use separate and nested elements for `.nav-item` and `.nav-link` as shown below.

Navbar

  * Home (current)
  * Features
  * Pricing
  * Dropdown link 

Action Another action Something else here



    
    
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="#">Navbar</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Features</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Pricing</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Dropdown link
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="#">Action</a>
              <a class="dropdown-item" href="#">Another action</a>
              <a class="dropdown-item" href="#">Something else here</a>
            </div>
          </li>
        </ul>
      </div>
    </nav>

### Forms

Place various form controls and components within a navbar with `.form-inline`.

Search
    
    
    <nav class="navbar navbar-light bg-light">
      <form class="form-inline">
        <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
      </form>
    </nav>

Immediate children elements in `.navbar` use flex layout and will default to `justify-content: between`. Use additional [flex utilities](/docs/4.3/utilities/flex/) as needed to adjust this behavior.

Navbar Search
    
    
    <nav class="navbar navbar-light bg-light">
      <a class="navbar-brand">Navbar</a>
      <form class="form-inline">
        <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
      </form>
    </nav>

Input groups work, too:

@
    
    
    <nav class="navbar navbar-light bg-light">
      <form class="form-inline">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text" id="basic-addon1">@</span>
          </div>
          <input type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
        </div>
      </form>
    </nav>

Various buttons are supported as part of these navbar forms, too. This is also a great reminder that vertical alignment utilities can be used to align different sized elements.

Main button Smaller button
    
    
    <nav class="navbar navbar-light bg-light">
      <form class="form-inline">
        <button class="btn btn-outline-success" type="button">Main button</button>
        <button class="btn btn-sm btn-outline-secondary" type="button">Smaller button</button>
      </form>
    </nav>

### Text

Navbars may contain bits of text with the help of `.navbar-text`. This class adjusts vertical alignment and horizontal spacing for strings of text.

Navbar text with an inline element 
    
    
    <nav class="navbar navbar-light bg-light">
      <span class="navbar-text">
        Navbar text with an inline element
      </span>
    </nav>

Mix and match with other components and utilities as needed.

Navbar w/ text

  * Home (current)
  * Features
  * Pricing

Navbar text with an inline element 
    
    
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="#">Navbar w/ text</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Features</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Pricing</a>
          </li>
        </ul>
        <span class="navbar-text">
          Navbar text with an inline element
        </span>
      </div>
    </nav>

## Color schemes

Theming the navbar has never been easier thanks to the combination of theming classes and `background-color` utilities. Choose from `.navbar-light` for use with light background colors, or `.navbar-dark` for dark background colors. Then, customize with `.bg-*` utilities.

Navbar

  * Home (current)
  * Features
  * Pricing
  * About

Search

Navbar

  * Home (current)
  * Features
  * Pricing
  * About

Search

Navbar

  * Home (current)
  * Features
  * Pricing
  * About

Search
    
    
    <nav class="navbar navbar-dark bg-dark">
      <!-- Navbar content -->
    </nav>
    
    <nav class="navbar navbar-dark bg-primary">
      <!-- Navbar content -->
    </nav>
    
    <nav class="navbar navbar-light" style="background-color: #e3f2fd;">
      <!-- Navbar content -->
    </nav>

## Containers

Although it’s not required, you can wrap a navbar in a `.container` to center it on a page or add one within to only center the contents of a fixed or static top navbar.

Navbar
    
    
    <div class="container">
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Navbar</a>
      </nav>
    </div>

When the container is within your navbar, its horizontal padding is removed at breakpoints lower than your specified `.navbar-expand{-sm|-md|-lg|-xl}` class. This ensures we’re not doubling up on padding unnecessarily on lower viewports when your navbar is collapsed.

Navbar
    
    
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container">
        <a class="navbar-brand" href="#">Navbar</a>
      </div>
    </nav>

## Placement

Use our [position utilities](/docs/4.3/utilities/position/) to place navbars in non-static positions. Choose from fixed to the top, fixed to the bottom, or stickied to the top (scrolls with the page until it reaches the top, then stays there). Fixed navbars use `position: fixed`, meaning they’re pulled from the normal flow of the DOM and may require custom CSS (e.g., `padding-top` on the `<body>`) to prevent overlap with other elements.

Also note that **`.sticky-top` uses `position: sticky`, which [isn’t fully supported in every browser](https://caniuse.com/#feat=css-sticky)**.

Default
    
    
    <nav class="navbar navbar-light bg-light">
      <a class="navbar-brand" href="#">Default</a>
    </nav>

Fixed top
    
    
    <nav class="navbar fixed-top navbar-light bg-light">
      <a class="navbar-brand" href="#">Fixed top</a>
    </nav>

Fixed bottom
    
    
    <nav class="navbar fixed-bottom navbar-light bg-light">
      <a class="navbar-brand" href="#">Fixed bottom</a>
    </nav>

Sticky top
    
    
    <nav class="navbar sticky-top navbar-light bg-light">
      <a class="navbar-brand" href="#">Sticky top</a>
    </nav>

## Responsive behaviors

Navbars can utilize `.navbar-toggler`, `.navbar-collapse`, and `.navbar-expand{-sm|-md|-lg|-xl}` classes to change when their content collapses behind a button. In combination with other utilities, you can easily choose when to show or hide particular elements.

For navbars that never collapse, add the `.navbar-expand` class on the navbar. For navbars that always collapse, don’t add any `.navbar-expand` class.

### Toggler

Navbar togglers are left-aligned by default, but should they follow a sibling element like a `.navbar-brand`, they’ll automatically be aligned to the far right. Reversing your markup will reverse the placement of the toggler. Below are examples of different toggle styles.

With no `.navbar-brand` shown in lowest breakpoint:

Hidden brand

  * Home (current)
  * Link
  * Disabled

Search
    
    
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
        <a class="navbar-brand" href="#">Hidden brand</a>
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
          </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
          <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
      </div>
    </nav>

With a brand name shown on the left and toggler on the right:

Navbar

  * Home (current)
  * Link
  * Disabled

Search
    
    
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="#">Navbar</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    
      <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
          </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
          <input class="form-control mr-sm-2" type="search" placeholder="Search">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
      </div>
    </nav>

With a toggler on the left and brand name on the right:

Navbar

  * Home (current)
  * Link
  * Disabled

Search
    
    
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <a class="navbar-brand" href="#">Navbar</a>
    
      <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
          </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
          <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
      </div>
    </nav>

### External content

Sometimes you want to use the collapse plugin to trigger hidden content elsewhere on the page. Because our plugin works on the `id` and `data-target` matching, that’s easily done!

##### Collapsed content

Toggleable via the navbar brand.
    
    
    <div class="pos-f-t">
      <div class="collapse" id="navbarToggleExternalContent">
        <div class="bg-dark p-4">
          <h5 class="text-white h4">Collapsed content</h5>
          <span class="text-muted">Toggleable via the navbar brand.</span>
        </div>
      </div>
      <nav class="navbar navbar-dark bg-dark">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
      </nav>
    </div>
  *[WCAG]: Web Content Accessibility Guidelines
  *[WAI]: Web Accessibility Initiative
  *[ARIA]: Accessible Rich Internet Applications
