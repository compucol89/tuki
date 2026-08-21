> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente publica: https://raw.githubusercontent.com/w3c/wcag/main/understanding/20/focus-visible.html
> Espejo oficial: w3c/wcag (GitHub)
> Capturado: 2026-08-21

# Understanding Focus Visible

## In brief

Goal
    Users know which element has keyboard focus.
What to do
    Ensure each item receiving focus has a visible indicator.
Why it's important
    Without a focus indicator, sighted keyboard users cannot operate the page.

## Intent of Focus Visible

The purpose of this success criterion is to help a person know which element has the keyboard focus. 

“Mode of operation” accounts for user agents which may not always show a focus indicator, or only show the focus indicator when the keyboard is used. User agents may optimize when the focus indicator is shown, such as only showing it when a keyboard is used. Authors are responsible for providing at least one mode of operation where the focus is visible. In most cases there is only one mode of operation so this success criterion applies. The focus indicator must not be time limited: when the keyboard focus is shown, it must remain visible.

There may be situations where mouse/pointer users could also benefit from having a visible focus indicator, even though they did not set focus to an element using the keyboard. As a best practice, consider still providing an explicit focus indicator for these cases.

Focus indicators are visual information required to identify the focused state of a user interface component. As generally focus indication is provided using non-text content (for instance, using borders/outlines), that means that they are also subject to Success Criterion [1.4.11 Non-text Contrast (Level AA)](non-text-contrast). See the [Relationship with Focus Visible](./non-text-contrast.html#related-focus) section of [](./non-text-contrast.html)Understanding 1.4.11 Non-text Contrast for details and examples.

Keyboard focus indicators can take different forms. While this criterion does not specify what that form is, [2.4.13 Focus Appearance (Level AAA)](focus-appearance) provides guidance on creating a consistent, visible indicator, dealing with issues such as its size and shape.

## Benefits of Focus Visible

  * This success criterion helps anyone who relies on the keyboard to operate the page, by letting them visually determine the component on which keyboard operations will interact at any point in time. 
  * People with attention limitations, short term memory limitations, or limitations in executive processes benefit by being able to discover where the focus is located. 



## Examples of Focus Visible

  * When text fields receive focus, a vertical bar is displayed in the field, indicating that the user can insert text, OR all of the text is highlighted, indicating that the user can type over the text. 
  * When a user interface control receives focus, a visible border is displayed around it. 



## Resources for Focus Visible

  * [Styling form controls with CSS, revisited](http://www.456bereastreet.com/archive/200701/styling_form_controls_with_css_revisited/)

{% # Data for associated techniques is defined in understanding/understanding.11tydata.js %} {% include "understanding/techniques.html" %} 
