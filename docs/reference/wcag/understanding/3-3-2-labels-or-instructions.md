> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente publica: https://raw.githubusercontent.com/w3c/wcag/main/understanding/20/labels-or-instructions.html
> Espejo oficial: w3c/wcag (GitHub)
> Capturado: 2026-08-21

# Understanding Labels or Instructions

## In brief

Goal
    Users know what information to enter.
What to do
    Provide labels or instructions for inputs.
Why it's important
    Everyone, especially those with cognitive disabilities, will know how to respond.

## Intent of Labels or Instructions

The intent of this success criterion is to have content authors present instructions or labels that identify the controls in a form so that users know what input data is expected. In the case of radio buttons, checkboxes, comboboxes, or similar controls that provide users with options, each option must have an appropriate label so that users know what they are actually selecting. Instructions or labels may also specify data formats for data entry fields, especially if they are out of the customary formats or if there are specific rules for correct input. Content authors may also choose to make such instructions available to users only when the individual control has focus especially when instructions are long and verbose. 

The intent of this success criterion is not to clutter the page with unnecessary information but to provide important cues and instructions that will benefit people with disabilities. Too much information or instruction can be just as harmful as too little. The goal is to make certain that enough information is provided for the user to accomplish the task without undue confusion or navigation. 

Note that the majority of form control labels are text-based. Using images as labels meets the requirements of the criterion, but care should be taken to ensure that the images are widely understood by the intended target audience. Authors may consider providing additional hints, such as text-based tooltips or supplementary text, to support clarity when using image-based labels.

This success criterion does not require that labels or instructions be correctly marked up, identified, or associated with their respective controls — that aspect is covered separately by [1.3.1 Info and Relationships](info-and-relationships). It is possible for content to pass this success criterion (providing relevant labels and instructions) while failing Success Criterion 1.3.1 (if the labels or instructions aren't correctly marked up, identified, or associated). 

Further, this success criterion does not take into consideration whether or not alternative methods of providing an accessible name or description for form controls and inputs has been used — that aspect is covered separately by [4.1.2 Name, Role, Value](name-role-value). It is possible for controls and inputs to have an appropriate accessible name or description (e.g. using `aria-label="..."`) and therefore pass Success Criterion 4.1.2, but to still fail this success criterion (if the labels or instructions aren't presented to all users, not just those using assistive technologies). 

This success criterion does not apply to links or other controls (such as an expand/collapse widget, or similar interactive components) that are not associated with data entry. 

While this success criterion requires that controls and inputs have labels or instructions, whether or not labels (if used) are accurate, sufficiently clear, or descriptive is covered separately by [2.4.6 Headings and Labels](headings-and-labels). 

The use of "requires" in this criterion's normative wording does not mean that the criterion only applies to _required_ form fields. It is used here as a synonym for "accepts", "expects", or "allows". The criterion applies to all form fields, whether they're required or optional. 

## Benefits of Labels or Instructions

  * Providing labels and instructions (including examples of expected data formats) helps all users — but particularly those with cognitive, language, and learning disabilities — to enter information correctly. 
  * Providing labels and instructions (including identification of required fields) can prevent users from making incomplete or incorrect form submissions, which prevents users from having to navigate once more through a page/form in order to fix submission errors. 



## Examples of Labels or Instructions

  * A field which asks the user to enter the two character abbreviation for a US state has a link next to it which will pop up an alphabetized list of state names and the correct abbreviation. 
  * A field for entering a date has text instructions to indicate the correct format for the date. 
  * On one website, a field with the text label of "username" is provided for someone to create a username to login to a website. On another website, there are strict rules about what characters can be used to create a username. On this website additional instructions would need to accompany the field to prevent users from encountering unnecessary errors. 
  * A website provides a global search field in the header of the site. Any term can be entered, so there are no instructions needed, but the field needs a cue to communicate its purpose. Commonly, such search field will be paired with a "loupe" or "magnify glass" search icon, serving as its visible label, if not also doubling as the visual identifier for the button that submits the search query. 
  * To enter their name, users are provided with two separate text fields. Rather than having a single label "Name" (which would appear to leave the second text field unlabelled), each field is given an explicit label — "Given Name" and "Family Name". 
  * A U.S. phone number separates the area code, exchange, and number into three fields. Parentheses surround the area code field, and a dash separates the exchange and number fields. While the punctuation provides visual clues to those familiar with the U.S. telephone number format, the punctuation is not sufficient to label the fields. The single "Phone number" label also cannot label all three fields. To address this, the three fields are grouped in a fieldset with the legend "Phone number". Visual labels for the fields (beyond the punctuation) cannot be provided in the design, so invisible labels are provided with the "title" attribute to each of the three fields. The value of this attribute for the three fields are, respectively, "Area Code", "Exchange", and "Number". 



## Resources for Labels or Instructions

{% # Data for associated techniques is defined in understanding/understanding.11tydata.js %} {% include "understanding/techniques.html" %} 
