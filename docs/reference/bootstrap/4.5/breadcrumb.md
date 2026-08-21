> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente: https://getbootstrap.com/docs/4.5/components/breadcrumb/
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



[View on GitHub](https://github.com/twbs/bootstrap/blob/v4.5.3/site/docs/4.5/components/breadcrumb.md "View and edit this file on GitHub")

# Breadcrumb

Indicate the current page’s location within a navigational hierarchy that automatically adds separators via CSS.

## Example

  1. Home



  1. Home
  2. Library



  1. Home
  2. Library
  3. Data


    
    
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">Home</li>
      </ol>
    </nav>
    
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Library</li>
      </ol>
    </nav>
    
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item"><a href="#">Library</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data</li>
      </ol>
    </nav>

## Changing the separator

Separators are automatically added in CSS through [`::before`](https://developer.mozilla.org/en-US/docs/Web/CSS/::before) and [`content`](https://developer.mozilla.org/en-US/docs/Web/CSS/content). They can be changed by changing `$breadcrumb-divider`. The [quote](https://sass-lang.com/documentation/modules/string#quote) function is needed to generate the quotes around a string, so if you want `>` as separator, you can use this:
    
    
    $breadcrumb-divider: quote(">");
    

It’s also possible to use a **base64 embedded SVG icon** :
    
    
    $breadcrumb-divider: url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPjxwYXRoIGQ9Ik0yLjUgMEwxIDEuNSAzLjUgNCAxIDYuNSAyLjUgOGw0LTQtNC00eiIgZmlsbD0iY3VycmVudENvbG9yIi8+PC9zdmc+);
    

The separator can be removed by setting `$breadcrumb-divider` to `none`:
    
    
    $breadcrumb-divider: none;
    

## Accessibility

Since breadcrumbs provide a navigation, it’s a good idea to add a meaningful label such as `aria-label="breadcrumb"` to describe the type of navigation provided in the `<nav>` element, as well as applying an `aria-current="page"` to the last item of the set to indicate that it represents the current page.

For more information, see the [WAI-ARIA Authoring Practices for the breadcrumb pattern](https://www.w3.org/TR/wai-aria-practices/#breadcrumb).
