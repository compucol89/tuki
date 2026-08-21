> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente publica: https://raw.githubusercontent.com/w3c/wcag/main/understanding/21/target-size-enhanced.html
> Espejo oficial: w3c/wcag (GitHub)
> Capturado: 2026-08-21

# Understanding SC 2.5.5 Target Size (Enhanced)

## In brief

Goal
    Controls can be operated more easily, especially on touchscreens.
What to do
    Make custom targets at least 44 by 44 pixels.
Why it's important
    Some people cannot tap small objects.

## Intent

The intent of this success criterion is to help users who may have trouble activating a small target because of hand tremors, limited dexterity, or other reasons. If the target is too small, it may be difficult to aim at the target. Mice and similar pointing devices can be hard to use for these users, and a larger target will help them greatly in having positive outcomes on the web page.

Touch is particularly problematic as it is an input mechanism with coarse precision. Users lack the same level of fine control as on inputs such as a mouse or stylus. A finger is larger than a mouse pointer, and generally obstructs the user's view of the precise location on the screen that is being touched/activated.

The issue can be further complicated for responsive and mobile sites that need to accommodate different types of fine and coarse inputs (for example, a site that can be accessed on a traditional desktop or laptop with a mouse, as well as on a tablet or mobile phone with a touchscreen).

While this criterion defines a minimum target size, it is recommended that larger sizes are used to reduce the possibility of unintentional actions. This is particularly relevant if any of the following are true:

  * the control is used frequently;
  * the result of the interaction cannot be easily undone;
  * the control is positioned where it will be difficult to reach, or is near the edge of the screen;
  * the control is part of a sequential task.



The targets on a screen can have different purposes and uses, and this success criterion specifies how each is to be handled.

**Equivalent targets:** If there is more than one target on a screen that performs the same action, only one of the targets need to meet the target size of 44 by 44 CSS pixels.

**Inline:** The success criterion does not apply to inline targets in sentences or blocks of text. Content is often designed to reflow based on the screen width available. This is known as responsive design and makes it easier to read since you do not need to scroll both horizontally and vertically. In reflowed content, the targets can appear anywhere on a line and can change position based on the width of the available screen. Since targets can appear anywhere on the line, the size cannot be larger than the available text and spacing between the sentences or paragraphs, otherwise the targets could overlap. It is for this reason targets which are contained within one or more sentences are excluded from the target size requirements.

If the target is the full sentence and the sentence is not in a block of text, then the target needs to be at least 44 by 44 CSS pixels.

A footnote or an icon within or at the end of a sentence is considered to be part of a sentence and therefore are excluded from the minimum target size.

**User Agent Control:** If the size of the target is not modified by the author through CSS or other size properties, then the target does not need to meet the target size of 44 by 44 CSS pixels.

**Essential:** If the target is required to be a particular target size and cannot be provided in another way, while changing it would essentially change the information or functionality of the content, then the target does not need to meet the target size of 44 by 44 CSS pixels.

## Benefits

  * Users who use a mobile device where a touchscreen is the primary mode of interaction
  * Users with mobility impairments, such as hand tremors
  * Users who use a mobile device in environments where they are exposed to shaking such as public transportation
  * Users who find fine motor movements difficult
  * Users who access a device using one hand
  * Users with large fingers, or who are operating the device with only a part of their finger or knuckle
  * Users who have low vision may better see the target



## Examples

Example 1: Buttons
    Three buttons are on-screen and the touch target area of each button is 44 by 44 CSS pixels.
Example 2: Equivalent target
    Multiple targets are provided on the page that perform the same function. One of the targets is 44 by 44 CSS pixels. The other targets do not have a minimum touch target of 44 by 44 CSS pixels.
Example 3: Text Link in a paragraph
    Links within a paragraph of text have varying touch target dimensions. Links within paragraphs of text do no need to meet the 44 by 44 CSS pixels requirements.
Example 4: Text Link in a sentence
    A text link that is in a sentence is excluded and does not need to meet the 44 by 44 CSS pixel requirement. If the text link is the full sentence, then the text link target touch area does need to meet the 44 by 44 CSS pixels.
Example 5: Footnote
    A footnote link at the end of a sentence does not need to meet the 44 by 44 CSS pixels requirements. The footnote at the end of the sentence is considered to be part of the sentence.
Example 6: Help icon
    A help icon within or at the end of a sentence does not need to meet the 44 by 44 CSS pixels requirements. The icon at the end of the sentence is considered to be part of the sentence.

## Resources

  * [Apple touch target size recommendations](https://developer.apple.com/design/human-interface-guidelines/ios/visual-design/adaptivity-and-layout/)
  * [Windows UWP Guidelines for touch targets](https://docs.microsoft.com/en-us/windows/apps/design/input/guidelines-for-targeting)
  * [Google Material Design Touch targets](https://material.io/design/layout/spacing-methods.html#touch-targets)
  * [web.dev Accessible tap targets](https://web.dev/accessible-tap-targets/)
  * [Human Fingertips to Investigate the Mechanics of Tactile Sense (PDF)](http://touchlab.mit.edu/publications/2003_009.pdf)
  * [One-Handed Thumb Use on Small Touchscreen Devices](https://www.cs.umd.edu/hcil/trs/2006-11/2006-11.htm)

{% # Data for associated techniques is defined in understanding/understanding.11tydata.js %} {% include "understanding/techniques.html" %} 
  *[WYSIWYG]: What You See Is What You Get
