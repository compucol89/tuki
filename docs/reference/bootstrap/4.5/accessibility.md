> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente: https://getbootstrap.com/docs/4.5/getting-started/accessibility/
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



  * Overview and Limitations
    * Structural markup
    * Interactive components
    * Color contrast
    * Visually hidden content
    * Reduced motion
  * Additional resources



[View on GitHub](https://github.com/twbs/bootstrap/blob/v4.5.3/site/docs/4.5/getting-started/accessibility.md "View and edit this file on GitHub")

# Accessibility

A brief overview of Bootstrap’s features and limitations for the creation of accessible content.

Bootstrap provides an easy-to-use framework of ready-made styles, layout tools, and interactive components, allowing developers to create websites and applications that are visually appealing, functionally rich, and accessible out of the box.

## Overview and Limitations

The overall accessibility of any project built with Bootstrap depends in large part on the author’s markup, additional styling, and scripting they’ve included. However, provided that these have been implemented correctly, it should be perfectly possible to create websites and applications with Bootstrap that fulfill [WCAG 2.0](https://www.w3.org/TR/WCAG20/) (A/AA/AAA), [Section 508](https://www.section508.gov/) and similar accessibility standards and requirements.

### Structural markup

Bootstrap’s styling and layout can be applied to a wide range of markup structures. This documentation aims to provide developers with best practice examples to demonstrate the use of Bootstrap itself and illustrate appropriate semantic markup, including ways in which potential accessibility concerns can be addressed.

### Interactive components

Bootstrap’s interactive components—such as modal dialogs, dropdown menus and custom tooltips—are designed to work for touch, mouse and keyboard users. Through the use of relevant [WAI-ARIA](https://www.w3.org/WAI/standards-guidelines/aria/) roles and attributes, these components should also be understandable and operable using assistive technologies (such as screen readers).

Because Bootstrap’s components are purposely designed to be fairly generic, authors may need to include further ARIA roles and attributes, as well as JavaScript behavior, to more accurately convey the precise nature and functionality of their component. This is usually noted in the documentation.

### Color contrast

Most colors that currently make up Bootstrap’s default palette—used throughout the framework for things such as button variations, alert variations, form validation indicators—lead to _insufficient_ color contrast (below the recommended [WCAG 2.0 color contrast ratio of 4.5:1](https://www.w3.org/TR/UNDERSTANDING-WCAG20/visual-audio-contrast-contrast.html)) when used against a light background. Authors will need to manually modify/extend these default colors to ensure adequate color contrast ratios.

### Visually hidden content

Content which should be visually hidden, but remain accessible to assistive technologies such as screen readers, can be styled using the `.sr-only` class. This can be useful in situations where additional visual information or cues (such as meaning denoted through the use of color) need to also be conveyed to non-visual users.
    
    
    <p class="text-danger">
      <span class="sr-only">Danger: </span>
      This action is not reversible
    </p>

For visually hidden interactive controls, such as traditional “skip” links, `.sr-only` can be combined with the `.sr-only-focusable` class. This will ensure that the control becomes visible once focused (for sighted keyboard users).
    
    
    <a class="sr-only sr-only-focusable" href="#content">Skip to main content</a>

### Reduced motion

Bootstrap includes support for the [`prefers-reduced-motion` media feature](https://drafts.csswg.org/mediaqueries-5/#prefers-reduced-motion). In browsers/environments that allow the user to specify their preference for reduced motion, most CSS transition effects in Bootstrap (for instance, when a modal dialog is opened or closed, or the sliding animation in carousels) will be disabled.

## Additional resources

  * [Web Content Accessibility Guidelines (WCAG) 2.0](https://www.w3.org/TR/WCAG20/)
  * [The A11Y Project](https://www.a11yproject.com/)
  * [MDN accessibility documentation](https://developer.mozilla.org/en-US/docs/Web/Accessibility)
  * [Tenon.io Accessibility Checker](https://tenon.io/)
  * [Colour Contrast Analyser (CCA)](https://developer.paciellogroup.com/resources/contrastanalyser/)
  * [“HTML Codesniffer” bookmarklet for identifying accessibility issues](https://github.com/squizlabs/HTML_CodeSniffer)


  *[WCAG]: Web Content Accessibility Guidelines
  *[WAI]: Web Accessibility Initiative
  *[ARIA]: Accessible Rich Internet Applications
  *[attr]: attribute
  *[HTML]: HyperText Markup Language
  *[RFS]: Responsive font sizes
