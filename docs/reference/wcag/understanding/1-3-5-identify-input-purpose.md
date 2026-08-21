> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente publica: https://raw.githubusercontent.com/w3c/wcag/main/understanding/21/identify-input-purpose.html
> Espejo oficial: w3c/wcag (GitHub)
> Capturado: 2026-08-21

# Understanding Identify Input Purpose

## In brief

Goal
    It is easier to fill out forms.
What to do
    Use code to indicate the purpose of common inputs, where technology allows.
Why it's important
    Some people with cognitive disabilities may not understand the input's purpose from the label alone.

## Intent of this Success Criterion

The intent of this success criterion is to ensure that the purpose of a form input collecting information about the user can be programmatically determined, so that user agents can extract and present this purpose to users using different modalities. The ability to programmatically declare the specific kind of data expected in a particular field makes filling out forms easier, especially for people with certain cognitive disabilities.

Appropriate visible labels and instruction can help users understand the purpose of form input fields, but users may benefit from having fields that collect specific types of information be rendered in an unambiguous, consistent, and possibly customized way for different modalities - either through defaults in their user agent, or through the aid of assistive technologies.

For some input fields, the `type` attribute already offers a way to broadly specify the intention of the input field, for example, `<input type="tel">`, `<input type="email">`, or `<input type="password">`. However, these are only very broad categories, describing the type of input, but not necessarily its purpose, especially as it relates to user-specific input fields. As an example, `type="email"` indicates that the field is for an email address but does not clarify if the purpose is for entering the user's email address or some other person's email.

This success criterion defines the types of user interface component input purposes, found in [Section 7 of the WCAG 2.1 Recommendation]({{ trUrl }}#input-purposes), that must be programmatically identifiable. When these user input purposes are present, and if the technology supports doing so, the field purpose must be programmatically identifiable.

The HTML `autocomplete` attribute only accepts a certain number of specific well-defined fixed values. This allows a more fine-grained definition or identification of purpose than the type attribute, for example, by allowing the author to specify a specific type of name: Name (`autocomplete="name"`), Given Name (`autocomplete="given-name"`), Family Name (`autocomplete="family-name"`), as well as Username (`autocomplete="username"`), and Nickname (`autocomplete="nickname"`).

By adopting and repurposing this predefined taxonomy of definitions, user agents and assistive technologies can now present the purpose of the inputs to users in different modalities. For example, assistive technologies may display familiar icons next to input fields to help users who have difficulties reading. An icon of a birthday cake may be shown in front of an input field with `autocomplete="bday"`, or the icon of a telephone in front of an input field with `autocomplete="tel"`.

In addition to repurposing this taxonomy, when the autocomplete attribute technique is used to meet this Success Criterion, browsers and other user agents can suggest and 'autofill' the right content by autocompleting these fields based on past user input stored in the browser. By defining more granular definitions of common input purposes, for example “Birthday” (`autocomplete="bday"`), browsers can store personalized values for each of these fields (the user's birthday date). The user is relieved of having to type the information and can instead confirm or, if needed, change the value of the field, a significant benefit for users with memory issues, dyslexia, and other disabilities. Because the `autocomplete` values are independent of language, users that may not be familiar with the text used to visually identify user input fields (the label) can still have that purpose consistently identified to them due to the fixed taxonomy of terms.

If an input field accepts two different types of input purpose (as in combined user name/user email fields) and the technology used does not allow multiple purpose values to be defined, it is valid to provide either one or the other value or leave out the designation of input purpose altogether.

When the user agent and assistive technology support for other metadata formats matures, metadata schemes like the [WAI-Adapt: Symbols Module](https://www.w3.org/TR/adapt-symbols/) may be used in addition or instead of the HTML autocomplete attribute to identify the purpose of input fields. They can also support automated adaptations that identify and match author-provided input labels to defined vocabularies or symbols that are used instead for labelling inputs.

This success criterion is specifically scoped to inputs collecting _information about the user_. The list of [Input Purposes]({{ trUrl }}#input-purposes) includes a few values that may not strictly be interpreted as directly relating to a user – most prominently, `transaction-amount`. An input field for information that is not _about the user_ does not need to programmatically expose its purpose, even if that purpose is included in the [Input Purposes]({{ trUrl }}#input-purposes) list.

The term "input" is used here as a generic way to refer to form controls that accept user input. For instance, in HTML, it is not limited to the `<input>` element, but also covers other controls such as `<textarea>` and `<select>`.

### Specific Benefits of Success Criterion 1.3.5

  * People with language and memory related disabilities or disabilities that affects executive function and decision-making benefit from the browser auto-filling personal information (such as name or address) when the autocomplete attribute is used to meet this Success Criterion, which means information does not need to be remembered by the user.
  * People with cerebral palsy, stroke, head injury, motor neuron disease or learning disability sometimes prefer images for communication. They can employ assistive technology which adds icons to input fields to communicate the purpose of the fields visually.
  * People with motor impairments also benefit from reducing the need for manual input when filling out forms.



## Examples of Success Criterion 1.3.5

A contact form using autofill
    A contact form auto-fills in the fields for name, street, post code, city, telephone number and email address from autofill values stored in the user's browser. Assistive technology can offer a customized way of identifying particular input fields, for example drawing on a set of symbols / icons that is familiar to the user, to communicate the purpose of the fields visually.
An order form with separate billing and shipping address
    A product order form fills in the address fields for billing address and a separate set of address fields for the shipping address, using the autofill detail tokens 'billing' and 'shipping'
A contact form using icons
    A browser plugin to add icons inserts icons representing the person's name, home address, telephone number and email address to identify the input purpose visually.

## Related Resources

  * [COGA Gap Analysis Table 3: Entering Data, Error Prevention, & Recovery](https://www.w3.org/TR/coga-gap-analysis/#table3)

{% # Data for associated techniques is defined in understanding/understanding.11tydata.js %} {% include "understanding/techniques.html" %} 
