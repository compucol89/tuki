> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente: https://getbootstrap.com/docs/4.5/content/tables/
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



  * Examples
  * Table head options
  * Striped rows
  * Bordered table
  * Borderless table
  * Hoverable rows
  * Small table
  * Contextual classes
  * Captions
  * Responsive tables
    * Always responsive
    * Breakpoint specific



[View on GitHub](https://github.com/twbs/bootstrap/blob/v4.5.3/site/docs/4.5/content/tables.md "View and edit this file on GitHub")

# Tables

Documentation and examples for opt-in styling of tables (given their prevalent use in JavaScript plugins) with Bootstrap.

## Examples

Due to the widespread use of tables across third-party widgets like calendars and date pickers, we’ve designed our tables to be **opt-in**. Just add the base class `.table` to any `<table>`, then extend with custom styles or our various included modifier classes.

Using the most basic table markup, here’s how `.table`-based tables look in Bootstrap. **All table styles are inherited in Bootstrap 4** , meaning any nested tables will be styled in the same manner as the parent.

# | First | Last | Handle  
---|---|---|---  
1 | Mark | Otto | @mdo  
2 | Jacob | Thornton | @fat  
3 | Larry | the Bird | @twitter  
      
    
    <table class="table">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">First</th>
          <th scope="col">Last</th>
          <th scope="col">Handle</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">1</th>
          <td>Mark</td>
          <td>Otto</td>
          <td>@mdo</td>
        </tr>
        <tr>
          <th scope="row">2</th>
          <td>Jacob</td>
          <td>Thornton</td>
          <td>@fat</td>
        </tr>
        <tr>
          <th scope="row">3</th>
          <td>Larry</td>
          <td>the Bird</td>
          <td>@twitter</td>
        </tr>
      </tbody>
    </table>

You can also invert the colors—with light text on dark backgrounds—with `.table-dark`.

# | First | Last | Handle  
---|---|---|---  
1 | Mark | Otto | @mdo  
2 | Jacob | Thornton | @fat  
3 | Larry | the Bird | @twitter  
      
    
    <table class="table table-dark">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">First</th>
          <th scope="col">Last</th>
          <th scope="col">Handle</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">1</th>
          <td>Mark</td>
          <td>Otto</td>
          <td>@mdo</td>
        </tr>
        <tr>
          <th scope="row">2</th>
          <td>Jacob</td>
          <td>Thornton</td>
          <td>@fat</td>
        </tr>
        <tr>
          <th scope="row">3</th>
          <td>Larry</td>
          <td>the Bird</td>
          <td>@twitter</td>
        </tr>
      </tbody>
    </table>

## Table head options

Similar to tables and dark tables, use the modifier classes `.thead-light` or `.thead-dark` to make `<thead>`s appear light or dark gray.

# | First | Last | Handle  
---|---|---|---  
1 | Mark | Otto | @mdo  
2 | Jacob | Thornton | @fat  
3 | Larry | the Bird | @twitter  
# | First | Last | Handle  
---|---|---|---  
1 | Mark | Otto | @mdo  
2 | Jacob | Thornton | @fat  
3 | Larry | the Bird | @twitter  
      
    
    <table class="table">
      <thead class="thead-dark">
        <tr>
          <th scope="col">#</th>
          <th scope="col">First</th>
          <th scope="col">Last</th>
          <th scope="col">Handle</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">1</th>
          <td>Mark</td>
          <td>Otto</td>
          <td>@mdo</td>
        </tr>
        <tr>
          <th scope="row">2</th>
          <td>Jacob</td>
          <td>Thornton</td>
          <td>@fat</td>
        </tr>
        <tr>
          <th scope="row">3</th>
          <td>Larry</td>
          <td>the Bird</td>
          <td>@twitter</td>
        </tr>
      </tbody>
    </table>
    
    <table class="table">
      <thead class="thead-light">
        <tr>
          <th scope="col">#</th>
          <th scope="col">First</th>
          <th scope="col">Last</th>
          <th scope="col">Handle</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">1</th>
          <td>Mark</td>
          <td>Otto</td>
          <td>@mdo</td>
        </tr>
        <tr>
          <th scope="row">2</th>
          <td>Jacob</td>
          <td>Thornton</td>
          <td>@fat</td>
        </tr>
        <tr>
          <th scope="row">3</th>
          <td>Larry</td>
          <td>the Bird</td>
          <td>@twitter</td>
        </tr>
      </tbody>
    </table>

## Striped rows

Use `.table-striped` to add zebra-striping to any table row within the `<tbody>`.

# | First | Last | Handle  
---|---|---|---  
1 | Mark | Otto | @mdo  
2 | Jacob | Thornton | @fat  
3 | Larry | the Bird | @twitter  
      
    
    <table class="table table-striped">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">First</th>
          <th scope="col">Last</th>
          <th scope="col">Handle</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">1</th>
          <td>Mark</td>
          <td>Otto</td>
          <td>@mdo</td>
        </tr>
        <tr>
          <th scope="row">2</th>
          <td>Jacob</td>
          <td>Thornton</td>
          <td>@fat</td>
        </tr>
        <tr>
          <th scope="row">3</th>
          <td>Larry</td>
          <td>the Bird</td>
          <td>@twitter</td>
        </tr>
      </tbody>
    </table>

# | First | Last | Handle  
---|---|---|---  
1 | Mark | Otto | @mdo  
2 | Jacob | Thornton | @fat  
3 | Larry | the Bird | @twitter  
      
    
    <table class="table table-striped table-dark">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">First</th>
          <th scope="col">Last</th>
          <th scope="col">Handle</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">1</th>
          <td>Mark</td>
          <td>Otto</td>
          <td>@mdo</td>
        </tr>
        <tr>
          <th scope="row">2</th>
          <td>Jacob</td>
          <td>Thornton</td>
          <td>@fat</td>
        </tr>
        <tr>
          <th scope="row">3</th>
          <td>Larry</td>
          <td>the Bird</td>
          <td>@twitter</td>
        </tr>
      </tbody>
    </table>

## Bordered table

Add `.table-bordered` for borders on all sides of the table and cells.

# | First | Last | Handle  
---|---|---|---  
1 | Mark | Otto | @mdo  
2 | Jacob | Thornton | @fat  
3 | Larry the Bird | @twitter  
      
    
    <table class="table table-bordered">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">First</th>
          <th scope="col">Last</th>
          <th scope="col">Handle</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">1</th>
          <td>Mark</td>
          <td>Otto</td>
          <td>@mdo</td>
        </tr>
        <tr>
          <th scope="row">2</th>
          <td>Jacob</td>
          <td>Thornton</td>
          <td>@fat</td>
        </tr>
        <tr>
          <th scope="row">3</th>
          <td colspan="2">Larry the Bird</td>
          <td>@twitter</td>
        </tr>
      </tbody>
    </table>

# | First | Last | Handle  
---|---|---|---  
1 | Mark | Otto | @mdo  
2 | Jacob | Thornton | @fat  
3 | Larry the Bird | @twitter  
      
    
    <table class="table table-bordered table-dark">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">First</th>
          <th scope="col">Last</th>
          <th scope="col">Handle</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">1</th>
          <td>Mark</td>
          <td>Otto</td>
          <td>@mdo</td>
        </tr>
        <tr>
          <th scope="row">2</th>
          <td>Jacob</td>
          <td>Thornton</td>
          <td>@fat</td>
        </tr>
        <tr>
          <th scope="row">3</th>
          <td colspan="2">Larry the Bird</td>
          <td>@twitter</td>
        </tr>
      </tbody>
    </table>

## Borderless table

Add `.table-borderless` for a table without borders.

# | First | Last | Handle  
---|---|---|---  
1 | Mark | Otto | @mdo  
2 | Jacob | Thornton | @fat  
3 | Larry the Bird | @twitter  
      
    
    <table class="table table-borderless">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">First</th>
          <th scope="col">Last</th>
          <th scope="col">Handle</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">1</th>
          <td>Mark</td>
          <td>Otto</td>
          <td>@mdo</td>
        </tr>
        <tr>
          <th scope="row">2</th>
          <td>Jacob</td>
          <td>Thornton</td>
          <td>@fat</td>
        </tr>
        <tr>
          <th scope="row">3</th>
          <td colspan="2">Larry the Bird</td>
          <td>@twitter</td>
        </tr>
      </tbody>
    </table>

`.table-borderless` can also be used on dark tables.

# | First | Last | Handle  
---|---|---|---  
1 | Mark | Otto | @mdo  
2 | Jacob | Thornton | @fat  
3 | Larry the Bird | @twitter  
      
    
    <table class="table table-borderless table-dark">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">First</th>
          <th scope="col">Last</th>
          <th scope="col">Handle</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">1</th>
          <td>Mark</td>
          <td>Otto</td>
          <td>@mdo</td>
        </tr>
        <tr>
          <th scope="row">2</th>
          <td>Jacob</td>
          <td>Thornton</td>
          <td>@fat</td>
        </tr>
        <tr>
          <th scope="row">3</th>
          <td colspan="2">Larry the Bird</td>
          <td>@twitter</td>
        </tr>
      </tbody>
    </table>

## Hoverable rows

Add `.table-hover` to enable a hover state on table rows within a `<tbody>`.

# | First | Last | Handle  
---|---|---|---  
1 | Mark | Otto | @mdo  
2 | Jacob | Thornton | @fat  
3 | Larry the Bird | @twitter  
      
    
    <table class="table table-hover">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">First</th>
          <th scope="col">Last</th>
          <th scope="col">Handle</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">1</th>
          <td>Mark</td>
          <td>Otto</td>
          <td>@mdo</td>
        </tr>
        <tr>
          <th scope="row">2</th>
          <td>Jacob</td>
          <td>Thornton</td>
          <td>@fat</td>
        </tr>
        <tr>
          <th scope="row">3</th>
          <td colspan="2">Larry the Bird</td>
          <td>@twitter</td>
        </tr>
      </tbody>
    </table>

# | First | Last | Handle  
---|---|---|---  
1 | Mark | Otto | @mdo  
2 | Jacob | Thornton | @fat  
3 | Larry the Bird | @twitter  
      
    
    <table class="table table-hover table-dark">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">First</th>
          <th scope="col">Last</th>
          <th scope="col">Handle</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">1</th>
          <td>Mark</td>
          <td>Otto</td>
          <td>@mdo</td>
        </tr>
        <tr>
          <th scope="row">2</th>
          <td>Jacob</td>
          <td>Thornton</td>
          <td>@fat</td>
        </tr>
        <tr>
          <th scope="row">3</th>
          <td colspan="2">Larry the Bird</td>
          <td>@twitter</td>
        </tr>
      </tbody>
    </table>

## Small table

Add `.table-sm` to make tables more compact by cutting cell padding in half.

# | First | Last | Handle  
---|---|---|---  
1 | Mark | Otto | @mdo  
2 | Jacob | Thornton | @fat  
3 | Larry the Bird | @twitter  
      
    
    <table class="table table-sm">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">First</th>
          <th scope="col">Last</th>
          <th scope="col">Handle</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">1</th>
          <td>Mark</td>
          <td>Otto</td>
          <td>@mdo</td>
        </tr>
        <tr>
          <th scope="row">2</th>
          <td>Jacob</td>
          <td>Thornton</td>
          <td>@fat</td>
        </tr>
        <tr>
          <th scope="row">3</th>
          <td colspan="2">Larry the Bird</td>
          <td>@twitter</td>
        </tr>
      </tbody>
    </table>

# | First | Last | Handle  
---|---|---|---  
1 | Mark | Otto | @mdo  
2 | Jacob | Thornton | @fat  
3 | Larry the Bird | @twitter  
      
    
    <table class="table table-sm table-dark">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">First</th>
          <th scope="col">Last</th>
          <th scope="col">Handle</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">1</th>
          <td>Mark</td>
          <td>Otto</td>
          <td>@mdo</td>
        </tr>
        <tr>
          <th scope="row">2</th>
          <td>Jacob</td>
          <td>Thornton</td>
          <td>@fat</td>
        </tr>
        <tr>
          <th scope="row">3</th>
          <td colspan="2">Larry the Bird</td>
          <td>@twitter</td>
        </tr>
      </tbody>
    </table>

## Contextual classes

Use contextual classes to color table rows or individual cells.

Class | Heading | Heading  
---|---|---  
Active | Cell | Cell  
Default | Cell | Cell  
Primary | Cell | Cell  
Secondary | Cell | Cell  
Success | Cell | Cell  
Danger | Cell | Cell  
Warning | Cell | Cell  
Info | Cell | Cell  
Light | Cell | Cell  
Dark | Cell | Cell  
      
    
    <!-- On rows -->
    <tr class="table-active">...</tr>
    
    <tr class="table-primary">...</tr>
    <tr class="table-secondary">...</tr>
    <tr class="table-success">...</tr>
    <tr class="table-danger">...</tr>
    <tr class="table-warning">...</tr>
    <tr class="table-info">...</tr>
    <tr class="table-light">...</tr>
    <tr class="table-dark">...</tr>
    
    <!-- On cells (`td` or `th`) -->
    <tr>
      <td class="table-active">...</td>
    
      <td class="table-primary">...</td>
      <td class="table-secondary">...</td>
      <td class="table-success">...</td>
      <td class="table-danger">...</td>
      <td class="table-warning">...</td>
      <td class="table-info">...</td>
      <td class="table-light">...</td>
      <td class="table-dark">...</td>
    </tr>

Regular table background variants are not available with the dark table, however, you may use [text or background utilities](/docs/4.5/utilities/colors/) to achieve similar styles.

# | Heading | Heading  
---|---|---  
1 | Cell | Cell  
2 | Cell | Cell  
3 | Cell | Cell  
4 | Cell | Cell  
5 | Cell | Cell  
6 | Cell | Cell  
7 | Cell | Cell  
8 | Cell | Cell  
9 | Cell | Cell  
      
    
    <!-- On rows -->
    <tr class="bg-primary">...</tr>
    <tr class="bg-success">...</tr>
    <tr class="bg-warning">...</tr>
    <tr class="bg-danger">...</tr>
    <tr class="bg-info">...</tr>
    
    <!-- On cells (`td` or `th`) -->
    <tr>
      <td class="bg-primary">...</td>
      <td class="bg-success">...</td>
      <td class="bg-warning">...</td>
      <td class="bg-danger">...</td>
      <td class="bg-info">...</td>
    </tr>

##### Conveying meaning to assistive technologies

Using color to add meaning only provides a visual indication, which will not be conveyed to users of assistive technologies – such as screen readers. Ensure that information denoted by the color is either obvious from the content itself (e.g. the visible text), or is included through alternative means, such as additional text hidden with the `.sr-only` class.

Create responsive tables by wrapping any `.table` with `.table-responsive{-sm|-md|-lg|-xl}`, making the table scroll horizontally at each `max-width` breakpoint of up to (but not including) 576px, 768px, 992px, and 1120px, respectively.

Note that since browsers do not currently support [range context queries](https://www.w3.org/TR/mediaqueries-4/#range-context), we work around the limitations of [`min-` and `max-` prefixes](https://www.w3.org/TR/mediaqueries-4/#mq-min-max) and viewports with fractional widths (which can occur under certain conditions on high-dpi devices, for instance) by using values with higher precision for these comparisons.

## Captions

A `<caption>` functions like a heading for a table. It helps users with screen readers to find a table and understand what it’s about and decide if they want to read it.

List of users # | First | Last | Handle  
---|---|---|---  
1 | Mark | Otto | @mdo  
2 | Jacob | Thornton | @fat  
3 | Larry | the Bird | @twitter  
      
    
    <table class="table">
      <caption>List of users</caption>
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">First</th>
          <th scope="col">Last</th>
          <th scope="col">Handle</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">1</th>
          <td>Mark</td>
          <td>Otto</td>
          <td>@mdo</td>
        </tr>
        <tr>
          <th scope="row">2</th>
          <td>Jacob</td>
          <td>Thornton</td>
          <td>@fat</td>
        </tr>
        <tr>
          <th scope="row">3</th>
          <td>Larry</td>
          <td>the Bird</td>
          <td>@twitter</td>
        </tr>
      </tbody>
    </table>

## Responsive tables

Responsive tables allow tables to be scrolled horizontally with ease. Make any table responsive across all viewports by wrapping a `.table` with `.table-responsive`. Or, pick a maximum breakpoint with which to have a responsive table up to by using `.table-responsive{-sm|-md|-lg|-xl}`.

##### Vertical clipping/truncation

Responsive tables make use of `overflow-y: hidden`, which clips off any content that goes beyond the bottom or top edges of the table. In particular, this can clip off dropdown menus and other third-party widgets.

### Always responsive

Across every breakpoint, use `.table-responsive` for horizontally scrolling tables.

# | Heading | Heading | Heading | Heading | Heading | Heading | Heading | Heading | Heading  
---|---|---|---|---|---|---|---|---|---  
1 | Cell | Cell | Cell | Cell | Cell | Cell | Cell | Cell | Cell  
2 | Cell | Cell | Cell | Cell | Cell | Cell | Cell | Cell | Cell  
3 | Cell | Cell | Cell | Cell | Cell | Cell | Cell | Cell | Cell  
      
    
    <div class="table-responsive">
      <table class="table">
        ...
      </table>
    </div>

### Breakpoint specific

Use `.table-responsive{-sm|-md|-lg|-xl}` as needed to create responsive tables up to a particular breakpoint. From that breakpoint and up, the table will behave normally and not scroll horizontally.

**These tables may appear broken until their responsive styles apply at specific viewport widths.**

# | Heading | Heading | Heading | Heading | Heading | Heading | Heading | Heading  
---|---|---|---|---|---|---|---|---  
1 | Cell | Cell | Cell | Cell | Cell | Cell | Cell | Cell  
2 | Cell | Cell | Cell | Cell | Cell | Cell | Cell | Cell  
3 | Cell | Cell | Cell | Cell | Cell | Cell | Cell | Cell  
      
    
    <div class="table-responsive-sm">
      <table class="table">
        ...
      </table>
    </div>

# | Heading | Heading | Heading | Heading | Heading | Heading | Heading | Heading  
---|---|---|---|---|---|---|---|---  
1 | Cell | Cell | Cell | Cell | Cell | Cell | Cell | Cell  
2 | Cell | Cell | Cell | Cell | Cell | Cell | Cell | Cell  
3 | Cell | Cell | Cell | Cell | Cell | Cell | Cell | Cell  
      
    
    <div class="table-responsive-md">
      <table class="table">
        ...
      </table>
    </div>

# | Heading | Heading | Heading | Heading | Heading | Heading | Heading | Heading  
---|---|---|---|---|---|---|---|---  
1 | Cell | Cell | Cell | Cell | Cell | Cell | Cell | Cell  
2 | Cell | Cell | Cell | Cell | Cell | Cell | Cell | Cell  
3 | Cell | Cell | Cell | Cell | Cell | Cell | Cell | Cell  
      
    
    <div class="table-responsive-lg">
      <table class="table">
        ...
      </table>
    </div>

# | Heading | Heading | Heading | Heading | Heading | Heading | Heading | Heading  
---|---|---|---|---|---|---|---|---  
1 | Cell | Cell | Cell | Cell | Cell | Cell | Cell | Cell  
2 | Cell | Cell | Cell | Cell | Cell | Cell | Cell | Cell  
3 | Cell | Cell | Cell | Cell | Cell | Cell | Cell | Cell  
      
    
    <div class="table-responsive-xl">
      <table class="table">
        ...
      </table>
    </div>
