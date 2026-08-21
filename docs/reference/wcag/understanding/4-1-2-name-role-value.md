> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente publica: https://raw.githubusercontent.com/w3c/wcag/main/understanding/20/name-role-value.html
> Espejo oficial: w3c/wcag (GitHub)
> Capturado: 2026-08-21

# Understanding Name, Role, Value

## In brief

Goal
    People using assistive technology understand all components.
What to do
    Give components correct names, roles, states, and values.
Why it's important
    Assistive technology only works well when code is done properly.

## Intent of Name, Role, Value

The intent of this success criterion is to ensure that Assistive Technologies (AT) can gather appropriate information about, activate (or set) and keep up to date on the status of user interface controls in the content. 

When standard controls from accessible technologies are used, this process is straightforward. If the user interface elements are used according to specification the conditions of this provision will be met. (See examples of Success Criterion 4.1.2 Name, Role, Value below) 

If custom controls are created, however, or interface elements are programmed (in code or script) to have a different role and/or function than usual, then additional measures need to be taken to ensure that the controls provide important and appropriate information to assistive technologies and allow themselves to be controlled by assistive technologies. 

What roles and states are appropriate to convey to assistive technology will depend on what the control represents. Specifics about such information are defined by other specifications, such as [WAI-ARIA](https://www.w3.org/TR/wai-aria/), or the relevant platform standards. Another factor to consider is whether there is sufficient accessibility support with assistive technologies to convey the information as specified. 

A particularly important state of a user interface control is whether or not it has focus. The focus state of a control can be programmatically determined, and notifications about change of focus are sent to user agents and assistive technology. Other examples of user interface control states are whether or not a checkbox or radio button has been selected, or whether a collapsible tree view or accordion is expanded or collapsed. 

Success Criterion 4.1.2 Name, Role, Value requires a programmatically determinable name for all user interface components. Names may be visible or invisible. Occasionally, the name needs to be visible, in which case it is identified as a label. Refer to the definition of name and label in the glossary for more information. 

## Benefits of Name, Role, Value

  * Providing role, state, and value information on all user interface components enables compatibility with assistive technology, such as screen readers, screen magnifiers, and speech recognition software, used by people with disabilities. 



## Examples of Name, Role, Value

Custom widgets using HTML and ARIA
    A page uses custom widgets – such as toggle buttons, comboboxes, or disclosure widgets – implemented using a combination of HTML and ARIA, to make sure that they programmatically convey their accessible name, their role, and their current state and value.

## Resources for Name, Role, Value

  * [Web Accessibility Initiative - Accessible Rich Internet Applications (ARIA)](https://www.w3.org/TR/wai-aria/)
  * [ARIA in HTML](https://www.w3.org/TR/html-aria/)
  * [Accessible Name and Description Computation 1.2](https://www.w3.org/TR/accname-1.2/)
  * [ARIA Authoring Practices Guide](https://www.w3.org/WAI/ARIA/apg/)

{% # Data for associated techniques is defined in understanding/understanding.11tydata.js %} {% include "understanding/techniques.html" %} 
  *[WYSIWYG]: What You See Is What You Get
  *[CAPTCHA]: Completely Automated Public Turing test to tell Computers and Humans Apart
