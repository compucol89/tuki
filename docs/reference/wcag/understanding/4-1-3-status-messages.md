> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente publica: https://www.w3.org/WAI/WCAG22/Understanding/status-messages.html
> Espejo oficial: w3c/wcag (GitHub)
> Capturado: 2026-08-21

# Status Messages  
Understanding SC 3.2.6

## In brief

Goal
    Make users aware of important changes in content.
What to do
    Let assistive technology notify users about status changes that don't take focus.
Why it's important
    People who do not see messages need to be informed about them.

## Intent of this Success Criterion

The intent of this success criterion is to make users aware of important changes in content that are not given focus, and to do so in a way that doesn't unnecessarily interrupt their work.

The intended beneficiaries are blind and low vision users of assistive technologies with screen reader capabilities. An additional benefit is that assistive technologies for users with cognitive disabilities may achieve an alternative means of indicating (or even delaying or suppressing) status messages, as preferred by the user.

The scope of this success criterion is specific to changes in content that involve status messages. A status message is a defined term in WCAG. There are two main criteria that determine whether something meets the definition of a status message:

  * the message "provides information to the user on the success or results of an action, on the waiting state of an application, on the progress of a process, or on the existence of errors;"
  * the message is not delivered via a change in context.



Information can be added to pages which does not meet the definition of a status message. For example, the list of results obtained from a search are not considered a status update and thus are not covered by this success criterion. However, brief text messages displayed _about_ the completion or status of the search, such as "Searching...", "18 results returned" or "No results returned" would be status updates if they do not take focus or cause a page refresh. Examples of status messages are given in the section titled Status message examples below.

This success criterion specifically addresses scenarios where new content is added to the page without changing the user's context. Changes of context, by their nature, interrupt the user by taking focus. They are already surfaced by assistive technologies, and so have already met the goal to alert the user to new content. As such, messages that involve changes of context do not need to be considered and are not within the scope of this success criterion. Examples of scenarios that add new content by changing the context are given in the section titled Examples of changes that are not status messages below.

While this criterion mentions "roles or properties", it applies more generally to programmatically determinable ways to convey status messages, including direct access to accessibility APIs that result in assistive technology alerts/announcements.

## Benefits

  * When appropriate roles or properties are assigned to status messages, the new content is spoken by screen readers in such a way as to assist blind and low vision users. Most sighted users can observe text peripherally added to the viewport. Such content provides additional information without affecting the user's current point of regard. The ability of an assistive technology to announce such new important text content allows more users to benefit from an awareness of the information in an equivalent manner.
  * Assigning proper roles or properties to status messages provides possible future uses and personalization opportunities, such as the potential to be exploited by assistive technologies created for users with some cognitive disabilities. Where page authors elect to design additions to the screen which do _not_ change the user's context (i.e., take focus), the information is arguably of less importance than something presented using a modal dialog, which must be acknowledged by the user. As such, depending on the user's preferences, an assistive technology may choose to delay, suppress, or transform such messages so a user is not unnecessarily interrupted; or conversely the assistive technology may highlight such messages where the user finds it optimal to do so.



## Examples of Success Criterion 3.2.6

### Status message examples

  * After a user presses a Search button, the page content is updated to include the results of the search, which are displayed in a section below the Search button. The change to content also includes the message "5 results returned" near the top of this new content. This text is given an appropriate role for a status message. A screen reader announces, "Five results returned".
  * After a user presses an Add to Shopping Cart button, a section of content near the Shopping Cart icon adds the text "5 items". A screen reader announces "Five items" or "Shopping cart, five items".
  * After a user enters incorrect text in an input called Postal Code, a message appears above the input reading "Invalid entry". The screen reader announces, "Invalid entry" or "Postal code, invalid entry".
  * After a user activates a process, an icon symbolizing 'busy' appears on the screen. The screen reader announces "application busy".
  * An application displays a dynamic progress bar to indicate the status of an upgrade. The screen reader provides intermittent announcements of the progress.
  * After a user submits a form, text is added to the existing form which reads, "Your form was successfully submitted." The screen reader announces the same message.
  * After a user unsuccessfully fills in a form because some of the data is in the incorrect format, text is added to the existing form which reads "5 errors on page". The screen reader announces the same message.
  * After a user puts a photo in an album in an online photo app, the app dynamically displays a popup message of "Saved in 'Wedding' album", which is also read by a screen reader.



### Examples of status messages that do not add new text to the screen

This success criterion was intentionally worded to apply primarily when visible text is added to (or becomes visible on) the page. The reason for this is that where new text is displayed, it is intended to be visible to all users. By providing a programmatic means of ensuring the text is also surfaced through assistive technologies, the success criterion provides the same information to users who cannot or may not see it. However, not all changes to content involve the addition of text to the screen. The following are all considerations relevant to this success criterion:

  * Non-displayed text specific to AT users;
  * Modification of status text;
  * Removal of status text; and
  * Non-textual status content, such as images.



#### Non-displayed text specific to AT users

There may be cases where the addition of visible text does not by itself convey sufficient information to the user of assistive technology. For example, the proximity of new content to other pieces of information on the screen may provide a visual context that is lacking in the text alone.

In such cases, authors may wish to designate additional content for inclusion in the status message, including non-displayed text which can be provided to the assistive technologies, for added context. Important considerations regarding the appropriate use of such techniques are further discussed in the Sufficient Techniques.

#### Modification of status text

If a status message persists on the page, modifications to this text are usually equivalent to a new status message. An example would be a shopping cart which updates text from reading "0 items" to "3 items". Typical methods of writing such changes in the page content result in the entire modified text string being considered a new change, and thus read by assistive technologies. However, where only the number in this string was coded as an updated chunk of content, the resulting experience for screen reader users could be to only hear "three", which may not be sufficient information to provide context for the user. In such situations, marking the entire "3 items" string as the status text would normally be a better solution. See Sufficient Techniques for more discussion, including the use of `aria-atomic`. In this case it would also be a courtesy to add offscreen text such as "in shopping cart" to the message.

#### Removal of status text

In situations where status text is entirely removed, its absence may itself convey information about the status. The most obvious example of this is where a message is displayed that the system is "busy" or "waiting". For a sighted user, when this text disappears, it is normally an indication that the state is now available. However non-sighted users would be unaware of this change, unless the end of the waiting state results in a change of context for the user. Where updating the visible message (e.g., to "system available") is not feasible, the use of a non-visible status message, such as "system available", ensures equivalent status information is provided. See Sufficient Techniques for more discussion.

#### Non-textual status content

Changes in content are not restricted to text changes. Where an icon or sound indicates a status message, this information will be surfaced by the screen reader through a combination of two things: 1) existing WCAG requirements governing text alternatives (under [Success Criterion 1.1.1 Non-Text Content](non-text-content)), and 2) the requirement of this current success criterion to supply an appropriate role.

### Examples of changes that are not status messages

The following examples identify situations where no additional author action is necessary. All cases are excepted from this success criterion since they do not meet the definition of "status messages."

  * An author displays an error message in a dialog.

Since the dialog takes focus, it is defined as a change of context and does not meet the definition of a status message. As a result of taking focus, the new change of context is already announced by the screen reader, and thus does not need to be included in the scope of this success criterion. 

  * Content is exposed or hidden when a user interacts with a user interface component, for example expanding components such as a menu, select, accordion or tree, or selecting a different tab item in a tablist.

None of the resulting changes to content meet the definition of status messages. Further, all components that meet the definition of a user interface component already have requirements specified under [4.1.2 Name, Role, Value](name-role-value), including the need to make notifications of changes to values and states available to user agents, including assistive technologies. As a result, changes in state, such as "expanded" or "collapsed," would be announced by the screen reader, and thus the user would be alerted to the 'addition' or 'removal' of content. As such, such content does not need to be addressed by this success criterion.

  * After a user completes a survey question which indicates they are unhappy, a series of new questions are added to the page about customer satisfaction.

The new inputs do not meet the definition of status message. They do not "provide information to the user on the success or results of an action, on the waiting state of an application, on the progress of a process or on the existence of errors," and so are not required to meet this success criterion.

Creating a status message about these questions being added, or notifying the user in advance that content changes may take place based on the user's response, are best practices but are not requirements in this scenario.




### Other uses of live regions or alerts

Live regions and alerts can be usefully applied in many situations where a change of content takes place which does not constitute a status message, as defined in this success criterion. However, there is a risk of making an application too "chatty" for a screen reader user. User testing should be carried out to ensure the appropriate level of feedback is achieved. The Advisory Techniques provide examples of how alerts or live regions can enhance the user experience.

The purpose of this success criterion is not to force authors to generate new status messages. Its intent is to ensure that when status messages _are_ displayed, they are programmatically identified in a way that allows assistive technologies to present them to the user.

{% # Data for associated techniques is defined in understanding/understanding.11tydata.js %} {% include "understanding/techniques.html" %} 
