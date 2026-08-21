> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente: https://getbootstrap.com/docs/4.3/content/typography/
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



  * Global settings
  * Headings
    * Customizing headings
  * Display headings
  * Lead
  * Inline text elements
  * Text utilities
  * Abbreviations
  * Blockquotes
    * Naming a source
    * Alignment
  * Lists
    * Unstyled
    * Inline
    * Description list alignment
  * Responsive font sizes



# Typography

Documentation and examples for Bootstrap typography, including global settings, headings, body text, lists, and more.

## Global settings

Bootstrap sets basic global display, typography, and link styles. When more control is needed, check out the [textual utility classes](/docs/4.3/utilities/text/).

  * Use a [native font stack](/docs/4.3/content/reboot/#native-font-stack) that selects the best `font-family` for each OS and device.
  * For a more inclusive and accessible type scale, we assume the browser default root `font-size` (typically 16px) so visitors can customize their browser defaults as needed.
  * Use the `$font-family-base`, `$font-size-base`, and `$line-height-base` attributes as our typographic base applied to the `<body>`.
  * Set the global link color via `$link-color` and apply link underlines only on `:hover`.
  * Use `$body-bg` to set a `background-color` on the `<body>` (`#fff` by default).



These styles can be found within `_reboot.scss`, and the global variables are defined in `_variables.scss`. Make sure to set `$font-size-base` in `rem`.

## Headings

All HTML headings, `<h1>` through `<h6>`, are available.

Heading | Example  
---|---  
`<h1></h1>` | h1. Bootstrap heading  
`<h2></h2>` | h2. Bootstrap heading  
`<h3></h3>` | h3. Bootstrap heading  
`<h4></h4>` | h4. Bootstrap heading  
`<h5></h5>` | h5. Bootstrap heading  
`<h6></h6>` | h6. Bootstrap heading  
      
    
    <h1>h1. Bootstrap heading</h1>
    <h2>h2. Bootstrap heading</h2>
    <h3>h3. Bootstrap heading</h3>
    <h4>h4. Bootstrap heading</h4>
    <h5>h5. Bootstrap heading</h5>
    <h6>h6. Bootstrap heading</h6>

`.h1` through `.h6` classes are also available, for when you want to match the font styling of a heading but cannot use the associated HTML element.

h1. Bootstrap heading

h2. Bootstrap heading

h3. Bootstrap heading

h4. Bootstrap heading

h5. Bootstrap heading

h6. Bootstrap heading
    
    
    <p class="h1">h1. Bootstrap heading</p>
    <p class="h2">h2. Bootstrap heading</p>
    <p class="h3">h3. Bootstrap heading</p>
    <p class="h4">h4. Bootstrap heading</p>
    <p class="h5">h5. Bootstrap heading</p>
    <p class="h6">h6. Bootstrap heading</p>

### Customizing headings

Use the included utility classes to recreate the small secondary heading text from Bootstrap 3.

###  Fancy display heading With faded secondary text
    
    
    <h3>
      Fancy display heading
      <small class="text-muted">With faded secondary text</small>
    </h3>

## Display headings

Traditional heading elements are designed to work best in the meat of your page content. When you need a heading to stand out, consider using a **display heading** —a larger, slightly more opinionated heading style. Keep in mind these headings are not responsive by default, but it’s possible to enable responsive font sizes.

Display 1  
---  
Display 2  
Display 3  
Display 4  
      
    
    <h1 class="display-1">Display 1</h1>
    <h1 class="display-2">Display 2</h1>
    <h1 class="display-3">Display 3</h1>
    <h1 class="display-4">Display 4</h1>

## Lead

Make a paragraph stand out by adding `.lead`.

Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Duis mollis, est non commodo luctus. 
    
    
    <p class="lead">
      Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Duis mollis, est non commodo luctus.
    </p>

## Inline text elements

Styling for common inline HTML5 elements.

You can use the mark tag to highlight text.

~~This line of text is meant to be treated as deleted text.~~

~~This line of text is meant to be treated as no longer accurate.~~

This line of text is meant to be treated as an addition to the document.

_This line of text will render as underlined_

This line of text is meant to be treated as fine print.

**This line rendered as bold text.**

_This line rendered as italicized text._
    
    
    <p>You can use the mark tag to <mark>highlight</mark> text.</p>
    <p><del>This line of text is meant to be treated as deleted text.</del></p>
    <p><s>This line of text is meant to be treated as no longer accurate.</s></p>
    <p><ins>This line of text is meant to be treated as an addition to the document.</ins></p>
    <p><u>This line of text will render as underlined</u></p>
    <p><small>This line of text is meant to be treated as fine print.</small></p>
    <p><strong>This line rendered as bold text.</strong></p>
    <p><em>This line rendered as italicized text.</em></p>

`.mark` and `.small` classes are also available to apply the same styles as `<mark>` and `<small>` while avoiding any unwanted semantic implications that the tags would bring.

While not shown above, feel free to use `<b>` and `<i>` in HTML5. `<b>` is meant to highlight words or phrases without conveying additional importance while `<i>` is mostly for voice, technical terms, etc.

## Text utilities

Change text alignment, transform, style, weight, and color with our [text utilities](/docs/4.3/utilities/text/) and [color utilities](/docs/4.3/utilities/colors/).

## Abbreviations

Stylized implementation of HTML’s `<abbr>` element for abbreviations and acronyms to show the expanded version on hover. Abbreviations have a default underline and gain a help cursor to provide additional context on hover and to users of assistive technologies.

Add `.initialism` to an abbreviation for a slightly smaller font-size.

attr

HTML
    
    
    <p><abbr title="attribute">attr</abbr></p>
    <p><abbr title="HyperText Markup Language" class="initialism">HTML</abbr></p>

## Blockquotes

For quoting blocks of content from another source within your document. Wrap `<blockquote class="blockquote">` around any HTML as the quote.

> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.
    
    
    <blockquote class="blockquote">
      <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
    </blockquote>

### Naming a source

Add a `<footer class="blockquote-footer">` for identifying the source. Wrap the name of the source work in `<cite>`.

> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.
> 
> Someone famous in Source Title
    
    
    <blockquote class="blockquote">
      <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
      <footer class="blockquote-footer">Someone famous in <cite title="Source Title">Source Title</cite></footer>
    </blockquote>

### Alignment

Use text utilities as needed to change the alignment of your blockquote.

> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.
> 
> Someone famous in Source Title
    
    
    <blockquote class="blockquote text-center">
      <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
      <footer class="blockquote-footer">Someone famous in <cite title="Source Title">Source Title</cite></footer>
    </blockquote>

> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.
> 
> Someone famous in Source Title
    
    
    <blockquote class="blockquote text-right">
      <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
      <footer class="blockquote-footer">Someone famous in <cite title="Source Title">Source Title</cite></footer>
    </blockquote>

## Lists

### Unstyled

Remove the default `list-style` and left margin on list items (immediate children only). **This only applies to immediate children list items** , meaning you will need to add the class for any nested lists as well.

  * Lorem ipsum dolor sit amet
  * Consectetur adipiscing elit
  * Integer molestie lorem at massa
  * Facilisis in pretium nisl aliquet
  * Nulla volutpat aliquam velit 
    * Phasellus iaculis neque
    * Purus sodales ultricies
    * Vestibulum laoreet porttitor sem
    * Ac tristique libero volutpat at
  * Faucibus porta lacus fringilla vel
  * Aenean sit amet erat nunc
  * Eget porttitor lorem


    
    
    <ul class="list-unstyled">
      <li>Lorem ipsum dolor sit amet</li>
      <li>Consectetur adipiscing elit</li>
      <li>Integer molestie lorem at massa</li>
      <li>Facilisis in pretium nisl aliquet</li>
      <li>Nulla volutpat aliquam velit
        <ul>
          <li>Phasellus iaculis neque</li>
          <li>Purus sodales ultricies</li>
          <li>Vestibulum laoreet porttitor sem</li>
          <li>Ac tristique libero volutpat at</li>
        </ul>
      </li>
      <li>Faucibus porta lacus fringilla vel</li>
      <li>Aenean sit amet erat nunc</li>
      <li>Eget porttitor lorem</li>
    </ul>

### Inline

Remove a list’s bullets and apply some light `margin` with a combination of two classes, `.list-inline` and `.list-inline-item`.

  * Lorem ipsum
  * Phasellus iaculis
  * Nulla volutpat


    
    
    <ul class="list-inline">
      <li class="list-inline-item">Lorem ipsum</li>
      <li class="list-inline-item">Phasellus iaculis</li>
      <li class="list-inline-item">Nulla volutpat</li>
    </ul>

### Description list alignment

Align terms and descriptions horizontally by using our grid system’s predefined classes (or semantic mixins). For longer terms, you can optionally add a `.text-truncate` class to truncate the text with an ellipsis.

Description lists
    A description list is perfect for defining terms.
Euismod
    

Vestibulum id ligula porta felis euismod semper eget lacinia odio sem nec elit.

Donec id elit non mi porta gravida at eget metus.

Malesuada porta
    Etiam porta sem malesuada magna mollis euismod.
Truncated term is truncated
    Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.
Nesting
    

Nested definition list
    Aenean posuere, tortor sed cursus feugiat, nunc augue blandit nunc.
    
    
    <dl class="row">
      <dt class="col-sm-3">Description lists</dt>
      <dd class="col-sm-9">A description list is perfect for defining terms.</dd>
    
      <dt class="col-sm-3">Euismod</dt>
      <dd class="col-sm-9">
        <p>Vestibulum id ligula porta felis euismod semper eget lacinia odio sem nec elit.</p>
        <p>Donec id elit non mi porta gravida at eget metus.</p>
      </dd>
    
      <dt class="col-sm-3">Malesuada porta</dt>
      <dd class="col-sm-9">Etiam porta sem malesuada magna mollis euismod.</dd>
    
      <dt class="col-sm-3 text-truncate">Truncated term is truncated</dt>
      <dd class="col-sm-9">Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</dd>
    
      <dt class="col-sm-3">Nesting</dt>
      <dd class="col-sm-9">
        <dl class="row">
          <dt class="col-sm-4">Nested definition list</dt>
          <dd class="col-sm-8">Aenean posuere, tortor sed cursus feugiat, nunc augue blandit nunc.</dd>
        </dl>
      </dd>
    </dl>

## Responsive font sizes

Bootstrap v4.3 ships with the option to enable responsive font sizes, allowing text to scale more naturally across device and viewport sizes. RFS can be enabled by changing the `$enable-responsive-font-sizes` Sass variable to `true` and recompiling Bootstrap.

To support RFS, we use a Sass mixin to replace our normal `font-size` properties. Responsive font sizes will be compiled into `calc()` functions with a mix of `rem` and viewport units to enable the responsive scaling behavior. More about RFS and its configuration can be found on its [GitHub repository](https://github.com/twbs/rfs).
  *[WCAG]: Web Content Accessibility Guidelines
  *[WAI]: Web Accessibility Initiative
  *[ARIA]: Accessible Rich Internet Applications
  *[attr]: attribute
  *[HTML]: HyperText Markup Language
  *[RFS]: Responsive font sizes
