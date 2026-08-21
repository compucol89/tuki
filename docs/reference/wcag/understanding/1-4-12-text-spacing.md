> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente publica: https://raw.githubusercontent.com/w3c/wcag/main/understanding/21/text-spacing.html
> Espejo oficial: w3c/wcag (GitHub)
> Capturado: 2026-08-21

# Understanding Text Spacing

## In brief

Goal
    Users can adjust text spacing to make it easier to read.
Author task
    Ensure content adapts to user-defined text settings.
Why it's important
    Some people need text with different spacing or font characteristics.

## Intent

The intent of this success criterion (SC) is to ensure that when people override author-specified text spacing to improve their reading experience, content is still readable and operable. Each of the requirements stipulated in the SC's four bullets helps ensure text styling can be adapted by the user to suit their needs.

The metrics set a target for a minimum for text spacing that must be met. Starting from the author's presentation, changing these four style properties to the specified values should not result in a loss of content or functionality.

This SC focuses on the adaptability of content to a change in spacing between lines, words, letters, and paragraphs. Any combination of these may assist a user with effectively reading text. As well, ensuring that content correctly adapts when users override author settings for spacing also significantly increases the likelihood other style preferences can be set by the user. For example, a user may need to change to a wider font family than the author has set in order to effectively read text. 

### Author Responsibility 

This SC does not dictate that authors must set all their content to the specified metrics, or provide a mechanism to do so. Rather, it specifies that an author's content has the ability to be set to those metrics without loss of content or functionality. The author requirement is both to not interfere with a user's ability to override the author settings, and to ensure that content thus modified does not break content in the manners shown in figures 1 through 3 in Effects of Not Allowing for Spacing Override. The values in the SC are a baseline. Authors are encouraged to allow spacing to surpass the values specified, not see them as a ceiling.

It is beneficial for users if authors use any locally available guidance for improving readability in the local language or writing system. Conversely, in some human languages and scripts, some of the metrics specified by the SC are inapplicable. For example, languages such as Japanese do not use spacing following paragraphs, meaning that users are unlikely to make any paragraph spacing changes in practice. The exception in this SC allows authors to ignore text style properties which are inapplicable to the combination of language and script being used. 

### User Responsibility

The user may choose to exceed the spacing adjustments in the SC. The ability to read and derive meaning from the overridden spacing rests with the user. If large changes in spacing reduce readability, or cause loss of content or functionality, the user can adjust or return to spacing within the bounds of the SC. How such changes are achieved is up to the user, who may choose a user stylesheet, bookmarklet, extension, or application. Regardless, the user needs the flexibility to adjust spacing – and within the bounds set in the SC, without loss of content or functionality.

Further, this SC does not require that content implement its own mechanisms to allow users to do this. It is not a failure of the content if a user agent or platform does not provide a way for users to do this. Content does not fail this SC if the method chosen by the user - for instance, the use of an extension or bookmarklet - fails to correctly set the line height and spacing text properties on the content (provided that the content is not actively and purposely preventing the properties from being added). 

### Applicability

If the markup-based technologies being used are capable of overriding text to the Success Criterion's metrics, then this SC is applicable. For instance Cascading Style Sheet/HTML technologies are quite able to allow for the specified spacing metrics. Plugin technologies would need to have a built-in ability to modify styles to the specified metrics. Currently, this SC does not apply to PDF, as the portable document format is not implemented using markup.

Examples of text typically not affected by style properties and not expected to adapt are:

  * Video captions embedded directly into the video frames and not provided as an associated caption file
  * Images of text



For this SC, [canvas](https://html.spec.whatwg.org/multipage/canvas.html#the-canvas-element) implementations of text are considered to be images of text.

#### Use of ellipses

There may be regions of a page where text containers cannot expand due to design constraints (such as a maximum width for the left navigation or table column headers). A common convention if text exceeds its space is to replace truncated text with an ellipsis. Where ellipses appear as a result of modifying text style properties, the page can still meet the Text Spacing requirements, so long as the content is still available. For example:

  * a mechanism is provided to reveal the truncated text on the page (for instance, the text appears on focus or on activation)
  * where the ellipsis is part of a section of content which includes a link, the truncated text is revealed on the linked page



Where text is not truncated but it is when text is spaced, if there is no mechanism to show the truncated text, it fails this success criterion.

### Effects of Not Allowing for Spacing Override

The following images show some types of failures when authors do not take into consideration that users may override spacing to the metrics specified in this success criterion.

#### Text Cut Off

The bottom portion of the words "Your Needs" is cut off in a heading making that text unreadable in Figure 1. It should read "We Provide a Mobile Application Service to Meet Your Needs."

Vertical text cut off is a failure.

In Figure 2 the last portion of text is cut off in three side-by-side headings. The first heading should read "A cog in the wheel" but it reads "A cog in the whe". Only half of the second "e" is visible and the letter "l" is completely missing. The second heading should read "A penny for your thoughts" but it reads "A penny for your". The third should read "Back to the drawing board." but it reads "Back to the drawi". 

Horizontal text cut off is a failure.

#### Text Overlap

In Figure 3 the last three words "Groups and Programs" of the heading "Technologists Seeking Input from Groups and Programs" overlap the following sentence. That sentence should read, "You are invited to share ideas and areas of interest related to the integration of technology from a group or program perspective." However, the words "You are invited to share ideas" are obscured and unreadable.

Overlapping text is a failure.

## Benefits

  * People with low vision who require increased space between lines, words, and letters are able to read text.
  * People with dyslexia may increase space between lines, words, and letters to increase reading speed.
  * Although not required by this SC, white space between blocks of text can help people with cognitive disabilities discern sections and call-out boxes.



## Examples

When spacing is being overridden to the SC's metrics:

  * Text fits within the bounds of its containing box without being cut off.
  * Text fits within the bounds of its containing box without overlapping other boxes.



## Resources

### Research

The grounds for this SC are based on research. The metrics chosen as measures are based on the [McLeish](http://journals.sagepub.com/doi/pdf/10.1177/0264619607075995) study. She ran from .04 to .25 em tests. McLeish found an increasing curve in reading speed of actual materials up to .25, but it started to flatten at .20. Previous studies that reported no improvement started at .5em. Right at the flat point. [Wayne E. Dick, Ph.D. analyzed the McLeish study](https://lists.w3.org/Archives/Public/public-low-vision-a11y-tf/2017Jun/0047.html) and translated from points. Dr. Dick recommended the metrics that the Working Group adopted.

#### Languages and Scripts

Roughly 480 different languages and scripts [have been tested](https://lists.w3.org/Archives/Public/public-low-vision-a11y-tf/2018Feb/0001.html). Maximum spacing adjustments allowed by the SC were set on the following 3 pages:

  * [Languages in their own writing systems](http://www.geonames.de/languages.html)
  * [Online Encyclopedia of writing systems and languages – language names](https://www.omniglot.com/language/names.htm)
  * [Universal Declaration of Human Rights](https://www.un.org/en/about-us/universal-declaration-of-human-rights)



#### Results

No adverse effects occurred. The following are the specific findings:

Character Spacing
    Individual characters in words remained intact though they were spaced a bit further apart.
Word Spacing
    Words were spaced further apart. In languages that typically do not include spaces between words (e.g., Japanese), applying word spacing had no effect. This is expected.
Line Height
    Changing line height did not separate diacritics from characters, nor did it adversely impact ascenders or descenders.

As previously discussed, the ability to read text with adjusted spacing is a user responsibility. This is true no matter the language.

The SC's exception addresses cases where a text style property is not used in a language or script. In such cases, authors are only required to ensure relevant properties do not break the layout.

### Other references

  * Allan, Kirkpatrick, Lawton Henry, Editors. (2017). [Accessibility Requirements for People with Low Vision (3.4 Spacing for Reading)](https://www.w3.org/TR/low-vision-needs/#spacing). World Wide Web Consortium.
  * [Stylus Team](https://github.com/openstyles/stylus/graphs/contributors) (2012). [Stylus browser extension](https://github.com/openstyles/stylus/blob/master/README.md) (Firefox, Chrome, and Opera) (compatible with Userstyles.org material).
  * Campbell, Alastair. (2017). [Text Adaptation Bookmarklet](https://github.com/alastc/adaptation-scripts/blob/master/scripts/text-adaptation.js). GitHub.
  * Chung, Susana T. L. (2012). [Dependence of Reading Speed on Letter Spacing in Central Vision Loss](https://www.ncbi.nlm.nih.gov/pmc/articles/PMC3429790/). Optom Vis Sci.
  * Chung, Susana T. L. (2002). [The Effect of Letter Spacing on Reading Speed in Central and Peripheral Vision (PDF)](http://journals.sagepub.com/doi/pdf/10.1177/0264619607075995). IOVS ARVO Journals.
  * Mcleish, Eve. (2007). [A study of the effect of letter spacing on the reading speed of young readers with low vision (PDF)](http://journals.sagepub.com/doi/pdf/10.1177/0264619607075995). The British Journal of Visual Impairment 25.2: 133-43.
  * Rello, L., & Baeza-Yates, R. A. (2017). [ How to present more readable text for people with dyslexia](https://link.springer.com/article/10.1007/s10209-015-0438-8). Universal Access in the Information Society, 16(1), 29-49.
  * Sjoblom, A.M., Eaton, E. and Stagg, S.D., (2016). [The effects of letter spacing and coloured overlays on reading speed and accuracy in adult dyslexia](http://onlinelibrary.wiley.com/doi/10.1111/bjep.12127/full). British Journal of Educational Psychology, 86(4), pp. 630-639). 
  * Zorzi, Marco et, al. (2012). [Extra-large letter spacing improves reading in dyslexia](http://www.pnas.org/content/109/28/11455.full). Proceedings of the National Academy of Sciences.

{% # Data for associated techniques is defined in understanding/understanding.11tydata.js %} {% include "understanding/techniques.html" %} 
