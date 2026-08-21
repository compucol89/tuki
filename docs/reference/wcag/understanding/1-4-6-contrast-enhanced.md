> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente publica: https://raw.githubusercontent.com/w3c/wcag/main/understanding/20/contrast-enhanced.html
> Espejo oficial: w3c/wcag (GitHub)
> Capturado: 2026-08-21

# Understanding Contrast (Enhanced)

## In brief

Goal
    Text can be seen by people who need strong contrast.
What to do
    Strongly contrast text against its background.
Why it's important
    Some people cannot read text with minimum contrast.

## Intent of Contrast (Enhanced)

The intent of this success criterion is to provide enough contrast between text and its background so that it can be read by people with moderately low vision (who do not use contrast-enhancing assistive technology). For people without color deficiencies, hue and saturation have minimal or no effect on legibility as assessed by reading performance (Knoblauch et al., 1991). Color deficiencies can affect luminance contrast somewhat. Therefore, in the recommendation, the contrast is calculated in such a way that color is not a key factor so that people who have a color vision deficit will also have adequate contrast between the text and the background. 

Text that is decorative and conveys no information is excluded. For example, if random words are used to create a background and the words could be rearranged or substituted without changing meaning, then it would be decorative and would not need to meet this criterion. 

Text that is larger and has wider character strokes is easier to read at lower contrast. The contrast requirement for larger text is therefore lower. This allows authors to use a wider range of color choices for large text, which is helpful for design of pages, particularly titles. 18 point text or 14 point bold text is judged to be large enough to require a lower contrast ratio. (See The American Printing House for the Blind Guidelines for Large Printing and The Library of Congress Guidelines for Large Print under Resources). "18 point" and "bold" can both have different meanings in different fonts but, except for very thin or unusual fonts, they should be sufficient. Since there are so many different fonts, the general measures are used and a note regarding thin or unusual fonts is included in the definition for large-scale text. 

When evaluating this Success Criterion, the font size in points should be obtained from the user agent or calculated on font metrics in the way that user agents do. Point sizes are based on the CSS `pt` size as defined in [CSS3 Values](https://www.w3.org/TR/css-values-3/#reference-pixel). The ratio between sizes in points and CSS pixels is `1pt = 1.333px`, therefore `14pt` and `18pt` are equivalent to approximately `18.5px` and `24px`. 

Because different image editing applications default to different pixel densities (e.g., `72ppi` or `96ppi`), specifying point sizes for fonts from within an image editing application can be unreliable when it comes to presenting text at a specific size. When creating images of large-scale text, authors should ensure that the text in the resulting image is roughly equivalent to 1.2 and 1.5 em or to 120% or 150% of the default size for body text. For example, for a `72ppi` image, an author would need to use approximately 19pt and 24pt font sizes in order to successfully present images of large-scale text to a user. 

The 7:1 and 4.5:1 contrast ratios referenced in this success criterion are intended to be treated as threshold values. When comparing the computed contrast ratio to the Success Criterion ratio, the computed values should not be rounded (e.g., 4.499:1 would not meet the 4.5:1 threshold).

Because authors do not have control over user settings for font smoothing/anti-aliasing, when evaluating this Success Criterion, refer to the foreground and background colors obtained from the user agent, or the underlying markup and stylesheets, rather than the text as presented on screen.

Due to anti-aliasing, particularly thin or unusual fonts may be rendered by user agents with a much fainter color than the actual text color defined in the underlying CSS. This can lead to situations where text has a contrast ratio that nominally passes the Success Criterion, but has a much lower contrast in practice. In these cases, best practice would be for authors to choose a font with stronger/thicker lines, or to aim for a foreground/background color combination that exceeds the normative requirements of this success criterion. 

The contrast requirements for text also apply to images of text (text that has been rendered into pixels and then stored in an image format) - see [Success Criterion 1.4.5 Images of Text](images-of-text). 

This requirement applies to situations in which images of text were intended to be understood as text. Incidental text, such as in photographs that happen to include a street sign, are not included. Nor is text that for some reason is designed to be invisible to all viewers. Stylized text, such as in corporate logos, should be treated in terms of its function on the page, which may or may not warrant including the content in the text alternative. Corporate visual guidelines beyond logo and logotype are not included in the exception. 

In this provision there is an exception that reads "that are part of a picture that contains significant other visual content,". This exception is intended to separate pictures that have text in them from images of text that are done to replace text in order to get a particular look. 

Images of text do not scale as well as text because they tend to pixelate. It is also harder to change foreground and background contrast and color combinations for images of text, which is necessary for some users. See [1.4.5 Images of Text](images-of-text). 

This success criterion applies to text in the page, including placeholder text and text that is shown when a pointer is hovering over an object or when an object has keyboard focus. If any of these are used in a page, the text needs to provide sufficient contrast. 

Although this success criterion only applies to text, similar issues occur for content presented in charts, graphs, diagrams, and other non-text-based information, which is covered by [Success Criterion 1.4.11 Non-Text Contrast](non-text-contrast). 

Text used as part of a logo or logotype is exempted from contrast requirements, under the assumption that logos/logotypes must comply with stricter color choices mandated by corporate identity or brand guidelines. However, this can be problematic when logos or logotypes act as user interface components (such as a link or other interactive control). In these cases, as a best practice, authors should consider choosing a variant of the logo or logotype that has sufficient text contrast, if allowed by the corporate identity or brand guidelines. Alternatively, authors should consider providing an equivalent user interface component which serves the same purpose and meets contrast requirements.

### Rationale for the Ratios Chosen

A contrast ratio of 3:1 is the minimum level recommended by [[ISO-9241-3]] and [[ANSI-HFES-100-1988]] for standard text and vision. The 4.5:1 ratio is used in Success Criterion 1.4.3 Contrast (Minimum) to account for the loss in contrast that results from moderately low visual acuity, congenital or acquired color deficiencies, or the loss of contrast sensitivity that typically accompanies aging. 

The rationale is based on a) adoption of the 3:1 contrast ratio for minimum acceptable contrast for normal observers, in the ANSI standard, and b) the empirical finding that in the population, visual acuity of 20/40 is associated with a contrast sensitivity loss of roughly 1.5 [[ARDITI-FAYE]]. A user with 20/40 would thus require a contrast ratio of `3 * 1.5 = 4.5 to 1`. Following analogous empirical findings and the same logic, the user with 20/80 visual acuity would require contrast of about 7:1. This ratio is used in this success criterion. 

Hues are perceived differently by users with color vision deficiencies (both congenital and acquired) resulting in different colors and relative luminance contrasts than for normally sighted users. Because of this, effective contrast and readability are different for this population. However, color deficiencies are so diverse that prescribing effective general use color pairs (for contrast) based on quantitative data is not feasible. Requiring good luminance contrast accommodates this by requiring contrast that is independent of color perception. Fortunately, most of the luminance contribution is from the mid and long wave receptors which largely overlap in their spectral responses. The result is that effective luminance contrast can generally be computed without regard to specific color deficiency, except for the use of predominantly long wavelength colors against darker colors (generally appearing black) for those who have protanopia. (We provide an advisory technique on avoiding red on black for that reason). For more information see [[ARDITI-KNOBLAUCH-1994]] [[ARDITI-KNOBLAUCH-1996]] [[ARDITI]]. 

Some people with cognitive disabilities require color combinations or hues that have low contrast, and therefore we allow and encourage authors to provide mechanisms to adjust the foreground and background colors of the content. Some of the combinations that could be chosen may have contrast levels that will be lower than those specified here. This is not a violation of this Success Criterion, provided there is a mechanism that will return to the required values set out here. 

The contrast ratio of 4.5:1 was chosen for level AA because it compensated for the loss in contrast sensitivity usually experienced by users with vision loss equivalent to approximately 20/40 vision. (20/40 calculates to approximately 4.5:1.) 20/40 is commonly reported as typical visual acuity of elders at roughly age 80. [[GITTINGS-FOZARD]] 

The contrast ratio of 7:1 was chosen for level AAA because it compensated for the loss in contrast sensitivity usually experienced by users with vision loss equivalent to approximately 20/80 vision. People with more than this degree of vision loss usually use assistive technologies to access their content (and the assistive technologies usually have contrast enhancing, as well as magnification capability built into them). The 7:1 level therefore generally provides compensation for the loss in contrast sensitivity experienced by users with low vision who do not use assistive technology and provides contrast enhancement for color deficiency as well. 

Calculations in [[ISO-9241-3]] and [[ANSI-HFES-100-1988]] are for body text. A relaxed contrast ratio is provided for text that is much larger.

### Notes on formula

Conversion from nonlinear to linear RGB values is based on IEC/4WD 61966-2-1 [[IEC-4WD]].

The formula (`L1/L2`) for contrast is based on [[ISO-9241-3]] and [[ANSI-HFES-100-1988]] standards.

The ANSI/HFS 100-1988 standard calls for the contribution from ambient light to be included in the calculation of L1 and L2. The `.05` value used is based on Typical Viewing Flare from [[IEC-4WD]]. 

This success criterion and its definitions use the terms "contrast ratio" and "relative luminance" rather than "luminance" to reflect the fact that web content does not emit light itself. The contrast ratio gives a measure of the relative luminance that would result when displayed. (Because it is a ratio, it is dimensionless.) 

Refer to related resources for a list of tools that utilize the contrast ratio to analyze the contrast of web content. 

See also [2.4.7 Focus Visible](focus-visible) for techniques for indicating keyboard focus. 

### Inactive User Interface Components

User Interface Components that are not available for user interaction (e.g., a disabled control in HTML) are not required to meet contrast requirements. An inactive user interface component is visible but not currently operable. An example would be a submit button at the bottom of a form that is visible but cannot be activated until all the required fields in the form are completed.

{% include "../21/img/1.4.11-ntc-button-inactive.svg" %}  An inactive button using default browser styles

## Benefits of Contrast (Enhanced)

  * People with low vision often have difficulty reading text that does not contrast with its background. This can be exacerbated if the person has a color vision deficiency that lowers the contrast even further. Providing a minimum luminance contrast ratio between the text and its background can make the text more readable even if the person does not see the full range of colors. It also works for the rare individuals who see no color. 



## Examples of Contrast (Enhanced)

## Resources for Contrast (Enhanced)

  * [Colour Contrast Analyser application](https://www.tpgi.com/color-contrast-checker/)
  * [Luminosity Colour Contrast Ratio Analyser](https://juicystudio.com/services/luminositycontrastratio.php)
  * [Colour Contrast Check](https://snook.ca/technical/colour_contrast/colour.html)
  * [Contrast Ratio Calculator](https://www.msfw.com/Services/ContrastRatioCalculator)
  * [Adobe Color - Color Contrast Analyzer Tool](https://color.adobe.com/create/color-contrast-analyzer)
  * [Atypical colour response](https://www.w3.org/Graphics/atypical-color-response)
  * [Colors On the Web Color Contrast Analyzer](http://www.colorsontheweb.com/colorcontrast.asp)
  * [Reading with Dyslexia - Fonts that can help alleviate visual stress.](https://www.iansyst.co.uk/fonts/)
  * [Good Fonts for Dyslexia - An Experimental Study](https://blog.dyslexia.com/good-fonts-for-dyslexia-an-experimental-study/)

{% # Data for associated techniques is defined in understanding/understanding.11tydata.js %} {% include "understanding/techniques.html" %} 
