> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente publica: https://raw.githubusercontent.com/w3c/wcag/main/understanding/20/no-keyboard-trap.html
> Espejo oficial: w3c/wcag (GitHub)
> Capturado: 2026-08-21

# Understanding No Keyboard Trap

## In brief

Goal
    Keyboard users don't get stuck.
What to do
    Ensure users always know how to navigate away from components.
Why it's important
    People who rely on the keyboard often have no other means to navigate.

## Intent of No Keyboard Trap

The intent of this success criterion is to ensure that content does not "trap" keyboard focus within subsections of content on a web page. This is a common problem when multiple formats are combined within a page and rendered using plug-ins or embedded applications, or when custom components and widgets are not implemented with keyboard users in mind.

There may be times when it's appropriate for a web page to restrict focus to a subsection of the content – for example, when the user is inside a modal dialog or popover. This does not fail the requirements of this criterion, as long as the user knows how to "untrap" the focus and leave that component.

Keyboard focus is not considered trapped when the user can navigate away from a component using only a keyboard interface, and if it only requires unmodified arrow or `Tab` keys or other "standard exit methods". This specification does not define what constitutes a "standard exit method" – this is dependent on the user's hardware, user agent, and operating system, and as such will require some interpretation from authors and auditors. Generally, in most environments with a physical keyboard, pressing the `Esc` key is a commonly used "standard exit method", but other platform-specific methods may be available.

If untrapping focus requires a different method (rather than unmodified arrow keys, the `Tab` key, or other "standard exit methods"), content can still pass this criterion provided that the user is advised how they can untrap focus using their keyboard interface.

## Benefits of No Keyboard Trap

  * People who rely on a keyboard or keyboard interface to use the web including people who are blind and people with physical disabilities. 



## Examples of No Keyboard Trap

A calendar widget
    A calendar widget allows users to add, remove or update items in their calendar using the keyboard. The controls in the widget are part of the tab order within the web page, allowing users to tab through the controls in the widget as well as to any links or controls that follow.
A puzzle applet
    Once a user tabs into an applet, further tabs and other keystrokes are handled by the applet. Instructions describing the keystroke used to exit the applet are provided prior to the applet as well as within the applet itself.
A modal dialog box
    A web application opens a dialog box. At the bottom of the dialog are two buttons, "Cancel" and "OK". When the dialog is open, focus is trapped within the dialog – tabbing from the last control in the dialog takes focus to the first control in the dialog (though, depending on implementation, the focus cycle might still include user agent controls). The dialog is dismissed by activating the "Cancel" button, the "OK" button, or the `Esc` key.
A WYSIWYG rich text editor
    Once the main editing area of a rich text editor receives focus, `Tab` / `Shift+Tab` are used to set the indentation of the current line being edited. When the editing area receives focus, an instruction appears, letting users know that they can use `Alt+F10` / `Option+F10` to move focus back out of the area and to the editor toolbar.

## Resources for No Keyboard Trap

{% # Data for associated techniques is defined in understanding/understanding.11tydata.js %} {% include "understanding/techniques.html" %} 
  *[WYSIWYG]: What You See Is What You Get
