> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente: https://getbootstrap.com/docs/4.5/components/modal/
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



  * How it works
  * Examples
    * Modal components
    * Live demo
    * Static backdrop
    * Scrolling long content
    * Vertically centered
    * Tooltips and popovers
    * Using the grid
    * Varying modal content
    * Change animation
    * Remove animation
    * Dynamic heights
    * Accessibility
    * Embedding YouTube videos
  * Optional sizes
  * Usage
    * Via data attributes
    * Via JavaScript
    * Options
    * Methods
      * .modal(options)
      * .modal('toggle')
      * .modal('show')
      * .modal('hide')
      * .modal('handleUpdate')
      * .modal('dispose')
    * Events



[View on GitHub](https://github.com/twbs/bootstrap/blob/v4.5.3/site/docs/4.5/components/modal.md "View and edit this file on GitHub")

# Modal

Use Bootstrap’s JavaScript modal plugin to add dialogs to your site for lightboxes, user notifications, or completely custom content.

## How it works

Before getting started with Bootstrap’s modal component, be sure to read the following as our menu options have recently changed.

  * Modals are built with HTML, CSS, and JavaScript. They’re positioned over everything else in the document and remove scroll from the `<body>` so that modal content scrolls instead.
  * Clicking on the modal “backdrop” will automatically close the modal.
  * Bootstrap only supports one modal window at a time. Nested modals aren’t supported as we believe them to be poor user experiences.
  * Modals use `position: fixed`, which can sometimes be a bit particular about its rendering. Whenever possible, place your modal HTML in a top-level position to avoid potential interference from other elements. You’ll likely run into issues when nesting a `.modal` within another fixed element.
  * Once again, due to `position: fixed`, there are some caveats with using modals on mobile devices. [See our browser support docs](/docs/4.5/getting-started/browsers-devices/#modals-and-dropdowns-on-mobile) for details.
  * Due to how HTML5 defines its semantics, [the `autofocus` HTML attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-autofocus) has no effect in Bootstrap modals. To achieve the same effect, use some custom JavaScript:


    
    
    $('#myModal').on('shown.bs.modal', function () {
      $('#myInput').trigger('focus')
    })

The animation effect of this component is dependent on the `prefers-reduced-motion` media query. See the [reduced motion section of our accessibility documentation](/docs/4.5/getting-started/accessibility/#reduced-motion).

Keep reading for demos and usage guidelines.

## Examples

### Modal components

Below is a _static_ modal example (meaning its `position` and `display` have been overridden). Included are the modal header, modal body (required for `padding`), and modal footer (optional). We ask that you include modal headers with dismiss actions whenever possible, or provide another explicit dismiss action.

##### Modal title

×

Modal body text goes here.

Close Save changes
    
    
    <div class="modal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Modal title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Modal body text goes here.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Save changes</button>
          </div>
        </div>
      </div>
    </div>

### Live demo

Toggle a working modal demo by clicking the button below. It will slide down and fade in from the top of the page.

##### Modal title

×

Woohoo, you're reading this text in a modal!

Close Save changes

Launch demo modal 
    
    
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
      Launch demo modal
    </button>
    
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            ...
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Save changes</button>
          </div>
        </div>
      </div>
    </div>

### Static backdrop

When backdrop is set to static, the modal will not close when clicking outside it. Click the button below to try it.

##### Modal title

×

I will not close if you click outside me. Don't even try to press escape key.

Close Understood

Launch static backdrop modal 
    
    
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#staticBackdrop">
      Launch static backdrop modal
    </button>
    
    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            ...
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Understood</button>
          </div>
        </div>
      </div>
    </div>

### Scrolling long content

When modals become too long for the user’s viewport or device, they scroll independent of the page itself. Try the demo below to see what we mean.

##### Modal title

×

Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.

Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.

Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.

Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.

Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.

Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.

Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.

Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.

Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.

Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.

Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.

Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.

Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.

Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.

Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.

Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.

Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.

Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.

Close Save changes

Launch demo modal 

You can also create a scrollable modal that allows scroll the modal body by adding `.modal-dialog-scrollable` to `.modal-dialog`.

##### Modal title

×

Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.

Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.

Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.

Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.

Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.

Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.

Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.

Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.

Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.

Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.

Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.

Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.

Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.

Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.

Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.

Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.

Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.

Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.

Close Save changes

Launch demo modal 
    
    
    <!-- Scrollable modal -->
    <div class="modal-dialog modal-dialog-scrollable">
      ...
    </div>

### Vertically centered

Add `.modal-dialog-centered` to `.modal-dialog` to vertically center the modal.

##### Modal title

×

Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.

Close Save changes

##### Modal title

×

Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.

Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.

Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.

Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.

Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.

Close Save changes

Vertically centered modal  Vertically centered scrollable modal 
    
    
    <!-- Vertically centered modal -->
    <div class="modal-dialog modal-dialog-centered">
      ...
    </div>
    
    <!-- Vertically centered scrollable modal -->
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      ...
    </div>

### Tooltips and popovers

[Tooltips](/docs/4.5/components/tooltips/) and [popovers](/docs/4.5/components/popovers/) can be placed within modals as needed. When modals are closed, any tooltips and popovers within are also automatically dismissed.

##### Modal title

×

##### Popover in a modal

This button triggers a popover on click.

* * *

##### Tooltips in a modal

This link and that link have tooltips on hover.

Close Save changes

Launch demo modal 
    
    
    <div class="modal-body">
      <h5>Popover in a modal</h5>
      <p>This <a href="#" role="button" class="btn btn-secondary popover-test" title="Popover title" data-content="Popover body content is set in this attribute.">button</a> triggers a popover on click.</p>
      <hr>
      <h5>Tooltips in a modal</h5>
      <p><a href="#" class="tooltip-test" title="Tooltip">This link</a> and <a href="#" class="tooltip-test" title="Tooltip">that link</a> have tooltips on hover.</p>
    </div>

### Using the grid

Utilize the Bootstrap grid system within a modal by nesting `.container-fluid` within the `.modal-body`. Then, use the normal grid system classes as you would anywhere else.

##### Grids in modals

×

.col-md-4

.col-md-4 .ml-auto

.col-md-3 .ml-auto

.col-md-2 .ml-auto

.col-md-6 .ml-auto

Level 1: .col-sm-9 

Level 2: .col-8 .col-sm-6 

Level 2: .col-4 .col-sm-6 

Close Save changes

Launch demo modal 
    
    
    <div class="modal-body">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">.col-md-4</div>
          <div class="col-md-4 ml-auto">.col-md-4 .ml-auto</div>
        </div>
        <div class="row">
          <div class="col-md-3 ml-auto">.col-md-3 .ml-auto</div>
          <div class="col-md-2 ml-auto">.col-md-2 .ml-auto</div>
        </div>
        <div class="row">
          <div class="col-md-6 ml-auto">.col-md-6 .ml-auto</div>
        </div>
        <div class="row">
          <div class="col-sm-9">
            Level 1: .col-sm-9
            <div class="row">
              <div class="col-8 col-sm-6">
                Level 2: .col-8 .col-sm-6
              </div>
              <div class="col-4 col-sm-6">
                Level 2: .col-4 .col-sm-6
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

### Varying modal content

Have a bunch of buttons that all trigger the same modal with slightly different contents? Use `event.relatedTarget` and [HTML `data-*` attributes](https://developer.mozilla.org/en-US/docs/Learn/HTML/Howto/Use_data_attributes) (possibly [via jQuery](https://api.jquery.com/data/)) to vary the contents of the modal depending on which button was clicked.

Below is a live demo followed by example HTML and JavaScript. For more information, read the modal events docs for details on `relatedTarget`.

Open modal for @mdo Open modal for @fat Open modal for @getbootstrap

##### New message

×

Recipient:

Message:

Close Send message
    
    
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo">Open modal for @mdo</button>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" data-whatever="@fat">Open modal for @fat</button>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" data-whatever="@getbootstrap">Open modal for @getbootstrap</button>
    
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">New message</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">
                <label for="recipient-name" class="col-form-label">Recipient:</label>
                <input type="text" class="form-control" id="recipient-name">
              </div>
              <div class="form-group">
                <label for="message-text" class="col-form-label">Message:</label>
                <textarea class="form-control" id="message-text"></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Send message</button>
          </div>
        </div>
      </div>
    </div>
    
    
    $('#exampleModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget) // Button that triggered the modal
      var recipient = button.data('whatever') // Extract info from data-* attributes
      // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
      // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
      var modal = $(this)
      modal.find('.modal-title').text('New message to ' + recipient)
      modal.find('.modal-body input').val(recipient)
    })

### Change animation

The `$modal-fade-transform` variable determines the transform state of `.modal-dialog` before the modal fade-in animation, the `$modal-show-transform` variable determines the transform of `.modal-dialog` at the end of the modal fade-in animation.

If you want for example a zoom-in animation, you can set `$modal-fade-transform: scale(.8)`.

### Remove animation

For modals that simply appear rather than fade in to view, remove the `.fade` class from your modal markup.
    
    
    <div class="modal" tabindex="-1" aria-labelledby="..." aria-hidden="true">
      ...
    </div>

### Dynamic heights

If the height of a modal changes while it is open, you should call `$('#myModal').modal('handleUpdate')` to readjust the modal’s position in case a scrollbar appears.

### Accessibility

Be sure to add `aria-labelledby="..."`, referencing the modal title, to `.modal`. Additionally, you may give a description of your modal dialog with `aria-describedby` on `.modal`. Note that you don’t need to add `role="dialog"` since we already add it via JavaScript.

### Embedding YouTube videos

Embedding YouTube videos in modals requires additional JavaScript not in Bootstrap to automatically stop playback and more. [See this helpful Stack Overflow post](https://stackoverflow.com/questions/18622508/bootstrap-3-and-youtube-in-modal) for more information.

## Optional sizes

Modals have three optional sizes, available via modifier classes to be placed on a `.modal-dialog`. These sizes kick in at certain breakpoints to avoid horizontal scrollbars on narrower viewports.

Size | Class | Modal max-width  
---|---|---  
Small | `.modal-sm` | `300px`  
Default | None | `500px`  
Large | `.modal-lg` | `800px`  
Extra large | `.modal-xl` | `1140px`  
  
Our default modal without modifier class constitutes the “medium” size modal.

Extra large modal Large modal Small modal
    
    
    <div class="modal-dialog modal-xl">...</div>
    <div class="modal-dialog modal-lg">...</div>
    <div class="modal-dialog modal-sm">...</div>

##### Extra large modal

×

... 

##### Large modal

×

... 

##### Small modal

×

... 

## Usage

The modal plugin toggles your hidden content on demand, via data attributes or JavaScript. It also adds `.modal-open` to the `<body>` to override default scrolling behavior and generates a `.modal-backdrop` to provide a click area for dismissing shown modals when clicking outside the modal.

### Via data attributes

Activate a modal without writing JavaScript. Set `data-toggle="modal"` on a controller element, like a button, along with a `data-target="#foo"` or `href="#foo"` to target a specific modal to toggle.
    
    
    <button type="button" data-toggle="modal" data-target="#myModal">Launch modal</button>

### Via JavaScript

Call a modal with id `myModal` with a single line of JavaScript:
    
    
    $('#myModal').modal(options)

### Options

Options can be passed via data attributes or JavaScript. For data attributes, append the option name to `data-`, as in `data-backdrop=""`.

Name | Type | Default | Description  
---|---|---|---  
backdrop | boolean or the string `'static'` | true | Includes a modal-backdrop element. Alternatively, specify `static` for a backdrop which doesn't close the modal on click.  
keyboard | boolean | true | Closes the modal when escape key is pressed  
focus | boolean | true | Puts the focus on the modal when initialized.  
show | boolean | true | Shows the modal when initialized.  
  
### Methods

#### Asynchronous methods and transitions

All API methods are **asynchronous** and start a **transition**. They return to the caller as soon as the transition is started but **before it ends**. In addition, a method call on a **transitioning component will be ignored**.

[See our JavaScript documentation for more information](/docs/4.5/getting-started/javascript/#asynchronous-functions-and-transitions).

#### `.modal(options)`

Activates your content as a modal. Accepts an optional options `object`.
    
    
    $('#myModal').modal({
      keyboard: false
    })

#### `.modal('toggle')`

Manually toggles a modal. **Returns to the caller before the modal has actually been shown or hidden** (i.e. before the `shown.bs.modal` or `hidden.bs.modal` event occurs).
    
    
    $('#myModal').modal('toggle')

#### `.modal('show')`

Manually opens a modal. **Returns to the caller before the modal has actually been shown** (i.e. before the `shown.bs.modal` event occurs).
    
    
    $('#myModal').modal('show')

#### `.modal('hide')`

Manually hides a modal. **Returns to the caller before the modal has actually been hidden** (i.e. before the `hidden.bs.modal` event occurs).
    
    
    $('#myModal').modal('hide')

#### `.modal('handleUpdate')`

Manually readjust the modal’s position if the height of a modal changes while it is open (i.e. in case a scrollbar appears).
    
    
    $('#myModal').modal('handleUpdate')

#### `.modal('dispose')`

Destroys an element’s modal.

### Events

Bootstrap’s modal class exposes a few events for hooking into modal functionality. All modal events are fired at the modal itself (i.e. at the `<div class="modal">`).

Event Type | Description  
---|---  
show.bs.modal | This event fires immediately when the `show` instance method is called. If caused by a click, the clicked element is available as the `relatedTarget` property of the event.  
shown.bs.modal | This event is fired when the modal has been made visible to the user (will wait for CSS transitions to complete). If caused by a click, the clicked element is available as the `relatedTarget` property of the event.  
hide.bs.modal | This event is fired immediately when the `hide` instance method has been called.  
hidden.bs.modal | This event is fired when the modal has finished being hidden from the user (will wait for CSS transitions to complete).  
hidePrevented.bs.modal | This event is fired when the modal is shown, its backdrop is `static` and a click outside the modal or an escape key press is performed with the keyboard option or `data-keyboard` set to `false`.  
      
    
    $('#myModal').on('hidden.bs.modal', function (e) {
      // do something...
    })
  *[WCAG]: Web Content Accessibility Guidelines
  *[WAI]: Web Accessibility Initiative
  *[ARIA]: Accessible Rich Internet Applications
  *[attr]: attribute
  *[HTML]: HyperText Markup Language
  *[RFS]: Responsive font sizes
