> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente publica: https://www.w3.org/TR/WCAG22/
> Espejo oficial: w3c/wcag tag WCAG22-20231005 (GitHub)
> Capturado: 2026-08-21

Web Content Accessibility Guidelines (WCAG) 2.2 covers a wide range of recommendations for making Web content more accessible. Following these guidelines will make content more accessible to a wider range of people with disabilities, including accommodations for blindness and low vision, deafness and hearing loss, limited movement, speech disabilities, photosensitivity, and combinations of these, and some accommodation for learning disabilities and cognitive limitations; but will not address every user need for people with these disabilities. These guidelines address accessibility of web content on desktops, laptops, tablets, and mobile devices. Following these guidelines will also often make Web content more usable to users in general.

WCAG 2.2 success criteria are written as testable statements that are not technology-specific. Guidance about satisfying the success criteria in specific technologies, as well as general information about interpreting the success criteria, is provided in separate documents. See [Web Content Accessibility Guidelines (WCAG) Overview](https://www.w3.org/WAI/standards-guidelines/wcag/) for an introduction and links to WCAG technical and educational material.

WCAG 2.2 extends [Web Content Accessibility Guidelines 2.1](https://www.w3.org/TR/WCAG21/) [[WCAG21]], which was published as a W3C Recommendation June 2018. Content that conforms to WCAG 2.2 also conforms to WCAG 2.0 and WCAG 2.1. The WG intends that for policies requiring conformance to WCAG 2.0 or WCAG 2.1, WCAG 2.2 can provide an alternate means of conformance. The publication of WCAG 2.2 does not deprecate or supersede WCAG 2.0 or WCAG 2.1. While WCAG 2.0 and WCAG 2.1 remain W3C Recommendations, the W3C advises the use of WCAG 2.2 to maximize future applicability of accessibility efforts. The W3C also encourages use of the most current version of WCAG when developing or updating Web accessibility policies.

To comment, [file an issue in the W3C WCAG GitHub repository](https://github.com/w3c/wcag/issues/new). Although the proposed Success Criteria in this document reference issues tracking discussion, the Working Group requests that public comments be filed as new issues, one issue per discrete comment. It is free to create a GitHub account to file issues. If filing issues in GitHub is not feasible, send email to [public-agwg-comments@w3.org](mailto:public-agwg-comments@w3.org?subject=WCAG%202.1%20public%20comment) ([comment archive](https://lists.w3.org/Archives/Public/public-agwg-comments/)).

## Introduction

### Background on WCAG 2

Web Content Accessibility Guidelines (WCAG) 2.2 defines how to make Web content more accessible to people with disabilities. Accessibility involves a wide range of disabilities, including visual, auditory, physical, speech, cognitive, language, learning, and neurological disabilities. Although these guidelines cover a wide range of issues, they are not able to address the needs of people with all types, degrees, and combinations of disability. These guidelines also make Web content more usable by older individuals with changing abilities due to aging and often improve usability for users in general.

WCAG 2.2 is developed through the [W3C process](https://www.w3.org/WAI/standards-guidelines/w3c-process/) in cooperation with individuals and organizations around the world, with a goal of providing a shared standard for Web content accessibility that meets the needs of individuals, organizations, and governments internationally. WCAG 2.2 builds on WCAG 2.0 [[WCAG20]] and WCAG 2.1 [[WCAG21]], which in turn built on WCAG 1.0 [[WAI-WEBCONTENT]] and is designed to apply broadly to different Web technologies now and in the future, and to be testable with a combination of automated testing and human evaluation. For an introduction to WCAG, see the [Web Content Accessibility Guidelines (WCAG) Overview](https://www.w3.org/WAI/standards-guidelines/wcag/).

Significant challenges were encountered in defining additional criteria to address cognitive, language, and learning disabilities, including a short timeline for development as well as challenges in reaching consensus on testability, implementability, and international considerations of proposals. Work will carry on in this area in future versions of WCAG. We encourage authors to refer to our supplemental guidance on [improving inclusion for people with disabilities, including learning and cognitive disabilities, people with low-vision, and more](https://www.w3.org/WAI/standards-guidelines/wcag/#supplement).

Web accessibility depends not only on accessible content but also on accessible Web browsers and other user agents. Authoring tools also have an important role in Web accessibility. For an overview of how these components of Web development and interaction work together, see:

  * **[Essential Components of Web Accessibility](https://www.w3.org/WAI/fundamentals/components/)**
  * **[User Agent Accessibility Guidelines (UAAG) Overview](https://www.w3.org/WAI/standards-guidelines/uaag/)**
  * **[Authoring Tool Accessibility Guidelines (ATAG) Overview](https://www.w3.org/WAI/standards-guidelines/atag/)**



Where this document refers to "WCAG 2" it is intended to mean any and all versions of WCAG that start with 2.

### WCAG 2 Layers of Guidance

The individuals and organizations that use WCAG vary widely and include Web designers and developers, policy makers, purchasing agents, teachers, and students. In order to meet the varying needs of this audience, several layers of guidance are provided including overall _principles_ , general _guidelines_ , testable _success criteria_ and a rich collection of _sufficient techniques_ , _advisory techniques_ , and _documented common failures_ with examples, resource links and code.

  * **Principles** \- At the top are four principles that provide the foundation for Web accessibility: _perceivable, operable, understandable, and robust_. See also [Understanding the Four Principles of Accessibility](https://www.w3.org/WAI/WCAG22/Understanding/intro#understanding-the-four-principles-of-accessibility).

  * **Guidelines** \- Under the principles are guidelines. The 13 guidelines provide the basic goals that authors should work toward in order to make content more accessible to users with different disabilities. The guidelines are not testable, but provide the framework and overall objectives to help authors understand the success criteria and better implement the techniques.

  * **Success Criteria** \- For each guideline, testable success criteria are provided to allow WCAG 2.2 to be used where requirements and conformance testing are necessary such as in design specification, purchasing, regulation, and contractual agreements. In order to meet the needs of different groups and different situations, three levels of conformance are defined: A (lowest), AA, and AAA (highest). Additional information on WCAG levels can be found in [Understanding Levels of Conformance](https://www.w3.org/WAI/WCAG22/Understanding/conformance#levels).

  * **Sufficient and Advisory Techniques** \- For each of the _guidelines_ and _success criteria_ in the WCAG 2.2 document itself, the working group has also documented a wide variety of _techniques_. The techniques are informative and fall into two categories: those that are _sufficient_ for meeting the success criteria and those that are _advisory_. The advisory techniques go beyond what is required by the individual success criteria and allow authors to better address the guidelines. Some advisory techniques address accessibility barriers that are not covered by the testable success criteria. Where common failures are known, these are also documented. See also [Sufficient and Advisory Techniques in Understanding WCAG 2.2](https://www.w3.org/WAI/WCAG22/Understanding/understanding-techniques).




All of these layers of guidance (principles, guidelines, success criteria, and sufficient and advisory techniques) work together to provide guidance on how to make content more accessible. Authors are encouraged to view and apply all layers that they are able to, including the advisory techniques, in order to best address the needs of the widest possible range of users.

Note that even content that conforms at the highest level (AAA) will not be accessible to individuals with all types, degrees, or combinations of disability, particularly in the cognitive, language, and learning areas. Authors are encouraged to consider the full range of techniques, including the advisory techniques, [Making Content Usable for People with Cognitive and Learning Disabilities](https://www.w3.org/TR/coga-usable/), as well as to seek relevant advice about current best practice to ensure that Web content is accessible, as far as possible, to this community. [Metadata](https://www.w3.org/WAI/WCAG22/Understanding/understanding-metadata) may assist users in finding content most suitable for their needs. 

### WCAG 2.2 Supporting Documents

The WCAG 2.2 document is designed to meet the needs of those who need a stable, referenceable technical standard. Other documents, called supporting documents, are based on the WCAG 2.2 document and address other important purposes, including the ability to be updated to describe how WCAG would be applied with new technologies. Supporting documents include: 

  1. **[How to Meet WCAG 2.2](https://www.w3.org/WAI/WCAG22/quickref/)** \- A customizable quick reference to WCAG 2.2 that includes all of the guidelines, success criteria, and techniques for authors to use as they are developing and evaluating Web content. This includes content from WCAG 2.0, 2.1 2.2 and can be filtered in many ways to help authors focus on relevant content.

  2. **[Understanding WCAG 2.2](https://www.w3.org/WAI/WCAG22/Understanding/)** \- A guide to understanding and implementing WCAG 2.2. There is a short "Understanding" document for each guideline and success criterion in WCAG 2.2 as well as key topics.

  3. **[Techniques for WCAG 2.2](https://www.w3.org/WAI/WCAG22/Techniques/)** \- A collection of techniques and common failures, each in a separate document that includes a description, examples, code and tests.

  4. **[The WCAG 2 Documents](https://www.w3.org/WAI/standards-guidelines/wcag/docs/)** \- A brief introduction to the WCAG 2 supporting documents and supplemental guidance.

  5. **[What's New in WCAG 2.2](https://www.w3.org/WAI/standards-guidelines/wcag/new-in-22/)** introduces the new success criteria with persona quotes that illustrate the accessibility issues. 




See [Web Content Accessibility Guidelines (WCAG) Overview](https://www.w3.org/WAI/standards-guidelines/wcag/) for a description of the WCAG 2.2 supporting material, including education resources related to WCAG 2. Additional resources covering topics such as the business case for Web accessibility, planning implementation to improve the accessibility of Web sites, and accessibility policies are listed in [WAI Resources](https://www.w3.org/WAI/Resources/Overview).

### Requirements for WCAG 2.2

WCAG 2.2 meets a set of [requirements for WCAG 2.2](https://w3c.github.io/wcag/requirements/22/) which, in turn, inherit requirements from previous WCAG 2 versions. Requirements structure the overall framework of guidelines and ensure backwards compatibility. The Working Group also used a less formal set of acceptance criteria for success criteria, to help ensure success criteria are similar in style and quality to those in WCAG 2.0. These requirements constrained what could be included in WCAG 2.2. This constraint was important to preserve its nature as a dot-release of WCAG 2.

### Comparison with WCAG 2.1

WCAG 2.2 was initiated with the goal to continue the work of WCAG 2.1: Improving accessibility guidance for three major groups: users with cognitive or learning disabilities, users with low vision, and users with disabilities on mobile devices. Many ways to meet these needs were proposed and evaluated, and a set of these were refined by the Working Group. Structural requirements inherited from WCAG 2.0, clarity and impact of proposals, and timeline led to the final set of success criteria included in this version. The Working Group considers that WCAG 2.2 incrementally advances web content accessibility guidance for all these areas, but underscores that not all user needs are met by these guidelines.

WCAG 2.2 builds on and is backwards compatible with WCAG 2.1, meaning web pages that conform to WCAG 2.2 are at least as accessible as pages that conform to WCAG 2.1. Requirements have been added that build on 2.1 and 2.0. WCAG 2.2 has removed one success criterion, 4.1.1 Parsing. Authors that are required by policy to conform with WCAG 2.0 or 2.1 will be able to update content to WCAG 2.2, but may need to continue to test and report 4.1.1. Authors following more than one version of the guidelines should be aware of the following additions.

#### New Features in WCAG 2.2

WCAG 2.2 extends WCAG 2.1 by adding new success criteria, definitions to support them, and guidelines to organize the additions. This additive approach helps to make it clear that sites which conform to WCAG 2.2 also conform to WCAG 2.1. The Accessibility Guidelines Working Group recommends that sites adopt WCAG 2.2 as their new conformance target, even if formal obligations mention previous versions, to provide improved accessibility and to anticipate future policy changes.

The following success criteria are new in WCAG 2.2:

  * 2.4.11 Focus Not Obscured (Minimum) (AA)
  * 2.4.12 Focus Not Obscured (Enhanced) (AAA)
  * 2.4.13 Focus Appearance (AAA)
  * 2.5.7 Dragging Movements (AA)
  * 2.5.8 Target Size (Minimum) (AA)
  * 3.2.6 Consistent Help (A)
  * 3.3.7 Redundant Entry (A)
  * 3.3.8 Accessible Authentication (Minimum) (AA)
  * 3.3.9 Accessible Authentication (Enhanced) (AAA)



The new success criteria may reference new terms that have also been added to the glossary and form part of the normative requirements of the success criteria.

WCAG 2.2 also introduces new sections detailing aspects of the specification which may impact privacy and security.

#### Numbering in WCAG 2.2

In order to avoid confusion for implementers for whom backwards compatibility to WCAG 2 versions is important, new success criteria in WCAG 2.2 have been appended to the end of the set of success criteria within their guideline. This avoids the need to change the section number of success criteria from WCAG 2, which would be caused by inserting new success criteria between existing success criteria in the guideline, but it means success criteria in each guideline are no longer grouped by conformance level. The order of success criteria within each guideline does not imply information about conformance level; only the conformance level indicator (A / AA / AAA) on the success criterion itself indicates this. The [WCAG 2.2 Quick Reference](https://www.w3.org/WAI/WCAG22/quickref/) will provide a way to view success criteria grouped by conformance level, along with many other filter and sort options.

#### Conformance to WCAG 2.2

WCAG 2.2 uses the same conformance model as WCAG 2.0. It is intended that sites that conform to WCAG 2.2 also conform to WCAG 2.0 and WCAG 2.1, which means they meet the requirements of any policies that reference WCAG 2.0 or WCAG 2.1, while also better meeting the needs of users on the current Web. 

### Later Versions of Accessibility Guidelines

In parallel with WCAG 2.2, the Accessibility Guidelines Working Group is developing another major version of accessibility guidelines. The result of this work is expected to be a more substantial restructuring of web accessibility guidance than would be realistic for dot-releases of WCAG 2. The work follows a research-focused, user-centered design methodology to produce the most effective and flexible outcome, including the roles of content authoring, user agent support, and authoring tool support. This is a multi-year effort, so WCAG 2.2 is needed as an interim measure to provide updated web accessibility guidance to reflect changes on the web since the publication of WCAG 2.0. The Working Group might also develop additional interim versions, continuing with WCAG 2.2, on a similar short timeline to provide additional support while the major version is completed. 

##  Perceivable 

Information and user interface components must be presentable to users in ways they can perceive.

### Text Alternatives

Provide text alternatives for any non-text content so that it can be changed into other forms people need, such as large print, braille, speech, symbols or simpler language.

### Time-based Media

Provide alternatives for time-based media.

### Adaptable

Create content that can be presented in different ways (for example simpler layout) without losing information or structure.

### Distinguishable

Make it easier for users to see and hear content including separating foreground from background.

## Operable 

User interface components and navigation must be operable.

### Keyboard Accessible

Make all functionality available from a keyboard.

### Enough Time

Provide users enough time to read and use content.

### Seizures and Physical Reactions

Do not design content in a way that is known to cause seizures or physical reactions.

### Navigable

Provide ways to help users navigate, find content, and determine where they are.

### Input Modalities

Make it easier for users to operate functionality through various inputs beyond keyboard.

##  Understandable 

Information and the operation of the user interface must be understandable.

### Readable

Make text content readable and understandable.

### Predictable

Make Web pages appear and operate in predictable ways.

### Input Assistance

Help users avoid and correct mistakes.

##  Robust 

Content must be robust enough that it can be interpreted by a wide variety of user agents, including assistive technologies.

### Compatible

Maximize compatibility with current and future user agents, including assistive technologies.

# Conformance

This section lists requirements for conformance to WCAG 2.2. It also gives information about how to make conformance claims, which are optional. Finally, it describes what it means to be accessibility supported, since only accessibility-supported ways of using technologies can be relied upon for conformance. [Understanding Conformance](https://www.w3.org/WAI/WCAG22/Understanding/conformance) includes further explanation of the accessibility-supported concept.

## Interpreting Normative Requirements

The main content of WCAG 2.2 is normative and defines requirements that impact conformance claims. Introductory material, appendices, sections marked as "non-normative", diagrams, examples, and notes are informative (non-normative). Non-normative material provides advisory information to help interpret the guidelines but does not create requirements that impact a conformance claim.

The key words MAY, MUST, MUST NOT, NOT RECOMMENDED, RECOMMENDED, SHOULD, and SHOULD NOT are to be interpreted as described in [[RFC2119]].

## Conformance Requirements

In order for a Web page to conform to WCAG 2.2, all of the following conformance requirements must be satisfied:

### Conformance Level

One of the following levels of conformance is met in full.

  * For Level A conformance (the minimum level of conformance), the Web page satisfies all the Level A Success Criteria, or a conforming alternate version is provided.
  * For Level AA conformance, the Web page satisfies all the Level A and Level AA Success Criteria, or a Level AA conforming alternate version is provided.
  * For Level AAA conformance, the Web page satisfies all the Level A, Level AA and Level AAA Success Criteria, or a Level AAA conforming alternate version is provided.



Although conformance can only be achieved at the stated levels, authors are encouraged to report (in their claim) any progress toward meeting success criteria from all levels beyond the achieved level of conformance.

It is not recommended that Level AAA conformance be required as a general policy for entire sites because it is not possible to satisfy all Level AAA Success Criteria for some content.

### Full pages

Conformance (and conformance level) is for full Web page(s) only, and cannot be achieved if part of a Web page is excluded.

For the purpose of determining conformance, alternatives to part of a page's content are considered part of the page when the alternatives can be obtained directly from the page, e.g., a long description or an alternative presentation of a video.

Authors of Web pages that cannot conform due to content outside of the author's control may consider a Statement of Partial Conformance.

A full page includes each variation of the page that is automatically presented by the page for various screen sizes (e.g. variations in a responsive Web page). Each of these variations needs to conform (or needs to have a conforming alternate version) in order for the entire page to conform.

### Complete processes

When a Web page is one of a series of Web pages presenting a process (i.e., a sequence of steps that need to be completed in order to accomplish an activity), all Web pages in the process conform at the specified level or better. (Conformance is not possible at a particular level if any page in the process does not conform at that level or better.)

An online store has a series of pages that are used to select and purchase products. All pages in the series from start to finish (checkout) conform in order for any page that is part of the process to conform.

### Only Accessibility-Supported Ways of Using Technologies

Only accessibility-supported ways of using technologies are relied upon to satisfy the success criteria. Any information or functionality that is provided in a way that is not accessibility supported is also available in a way that is accessibility supported. (See [Understanding accessibility support](https://www.w3.org/WAI/WCAG22/Understanding/conformance#accessibility-support).)

### Non-Interference

If  technologies  are used in a way that is not accessibility supported, or if they are used in a non-conforming way, then they do not block the ability of users to access the rest of the page. In addition, the Web page as a whole continues to meet the conformance requirements under each of the following conditions:

  1. when any technology that is not relied upon is turned on in a user agent,
  2. when any technology that is not relied upon is turned off in a user agent, and
  3. when any technology that is not relied upon is not supported by a user agent



In addition, the following success criteria apply to all content on the page, including content that is not otherwise relied upon to meet conformance, because failure to meet them could interfere with any use of the page:

  * **1.4.2 - Audio Control** ,
  * **2.1.2 - No Keyboard Trap** ,
  * **2.3.1 - Three Flashes or Below Threshold** , and
  * **2.2.2 - Pause, Stop, Hide**.



If a page cannot conform (for example, a conformance test page or an example page), it cannot be included in the scope of conformance or in a conformance claim.

For more information, including examples, see [Understanding Conformance Requirements](https://www.w3.org/WAI/WCAG22/Understanding/conformance#conformance-requirements).

## Conformance Claims (Optional) 

Conformance is defined only for Web pages. However, a conformance claim may be made to cover one page, a series of pages, or multiple related Web pages.

### Required Components of a Conformance Claim

Conformance claims are **not required**. Authors can conform to WCAG 2.2 without making a claim. However, if a conformance claim is made, then the conformance claim **must** include the following information:

  1. **Date** of the claim
  2. **Guidelines title, version and URI** "Web Content Accessibility Guidelines 2.2 at <https://www.w3.org/TR/WCAG22/>"
  3. **Conformance level** satisfied: (Level A, AA or AAA)
  4. **A concise description of the Web pages** , such as a list of URIs for which the claim is made, including whether subdomains are included in the claim.

The Web pages may be described by list or by an expression that describes all of the URIs included in the claim.

Web-based products that do not have a URI prior to installation on the customer's Web site may have a statement that the product would conform when installed.

  5. A list of the **Web content technologies relied upon**.



If a conformance logo is used, it would constitute a claim and must be accompanied by the required components of a conformance claim listed above.

### Optional Components of a Conformance Claim 

In addition to the required components of a conformance claim above, consider providing additional information to assist users. Recommended additional information includes:

  * A list of success criteria beyond the level of conformance claimed that have been met. This information should be provided in a form that users can use, preferably machine-readable metadata.
  * A list of the specific technologies that are " _used but notrelied upon_."
  * A list of user agents, including assistive technologies that were used to test the content.
  * A list of specific accessibility characteristics of the content, provided in machine-readable metadata.
  * Information about any additional steps taken that go beyond the success criteria to enhance accessibility.
  * A machine-readable metadata version of the list of specific technologies that are relied upon.
  * A machine-readable metadata version of the conformance claim.



Refer to [Understanding Conformance Claims](https://www.w3.org/WAI/WCAG22/Understanding/conformance#conformance-claims) for more information and example conformance claims.

Refer to [Understanding Metadata](https://www.w3.org/WAI/WCAG22/Understanding/understanding-metadata) for more information about the use of metadata in conformance claims.

## Statement of Partial Conformance - Third Party Content

Web pages that will later have additional content added can use a 'statement of partial conformance'. For example, an email program, a blog, an article that allows users to add comments, or applications supporting user-contributed content. Another example would be a page, such as a portal or news site, composed of content aggregated from multiple contributors, or sites that automatically insert content from other sources over time, such as when advertisements are inserted dynamically.

In these cases, it is not possible to know at the time of original posting what the uncontrolled content of the pages will be. It is important to note that the uncontrolled content can affect the accessibility of the controlled content as well. Two options are available:

  1. A determination of conformance can be made based on best knowledge. If a page of this type is monitored and repaired (non-conforming content is removed or brought into conformance) within two business days, then a determination or claim of conformance can be made since, except for errors in externally contributed content which are corrected or removed when encountered, the page conforms. No conformance claim can be made if it is not possible to monitor or correct non-conforming content;

**OR**

  2. A "statement of partial conformance" may be made that the page does not conform, but could conform if certain parts were removed. The form of that statement would be, "This page does not conform, but would conform to WCAG 2.2 at level X if the following parts from uncontrolled sources were removed." In addition, the following would also be true of uncontrolled content that is described in the statement of partial conformance:

     1. It is not content that is under the author's control.
     2. It is described in a way that users can identify (e.g., they cannot be described as "all parts that we do not control" unless they are clearly marked as such.)



## Statement of Partial Conformance - Language

A "statement of partial conformance due to language" may be made when the page does not conform, but would conform if accessibility support existed for (all of) the language(s) used on the page. The form of that statement would be, "This page does not conform, but would conform to WCAG 2.2 at level X if accessibility support existed for the following language(s):"

## Privacy Considerations

Success Criteria within this specification which the Working Group has identified possible implications for privacy, either by providing protections for end users or which are important for web site providers to take in to consideration when implementing features designed to protect user privacy, are listed below. This list reflects the current understanding of the Working Group but other Success Criteria may have privacy implications that the Working Group is not aware of at the time of publishing.

Success Criteria within this specification that may relate to privacy are:

  * 2.2.6 Timeouts (AAA)
  * 3.3.7 Redundant Entry (A)



## Security Considerations

Success Criteria within this specification which the Working Group has identified possible implications for security, either by providing protections for end users or which are important for web site providers to take in to consideration when implementing features designed to protect user security, are listed below. This list reflects the current understanding of the Working Group but other Success Criteria may have security implications that the Working Group is not aware of at the time of publishing.

Success Criteria within this specification that may relate to security are:

  * 1.1.1 Non-text Content (A)
  * 1.3.5 Identify Input Purpose (AA)
  * 1.4.7 Low or No Background Audio (AAA)
  * 2.2.1 Timing Adjustable (A)
  * 2.2.5 Re-authenticating (AAA)
  * 2.2.6 Timeouts (AAA)
  * 2.5.6 Concurrent Input Mechanisms (AAA)
  * 3.3.3 Error Suggestion (AA)
  * 3.3.7 Redundant Entry (A)
  * 3.3.8 Accessible Authentication (Minimum) (AA)
  * 3.3.9 Accessible Authentication (Enhanced) (AAA)



# Glossary

## Change Log

This section shows substantive changes made in WCAG 2.2 since WCAG 2.1. [Errata fixes to WCAG 2.1](https://www.w3.org/WAI/WCAG21/errata/) have also been incorporated into WCAG 2.2.

The full [commit history to WCAG 2.2](https://github.com/w3c/wcag/commits/main/guidelines) is available.

  * 2019-11-10: Promoted Focus Visible from Level AA to Level A.
  * 2020-01-14: Added "Focus Visible (Enhanced)", later renamed to Focus Appearance (Enhanced), later removed.
  * 2020-03-10: Renamed "Pointer Target Spacing" to "Target Size (Minimum)"
  * 2020-03-30: Added Accessible Authentication (Minimum).
  * 2020-05-27: Added "Dragging" (later renamed Dragging Movements).
  * 2020-07-19: Added "Findable Help" (later renamed to Consistent Help), "Fixed Reference Points" (Page Break Navigation), "Hidden Controls" (later renamed Visible Controls), "Pointer Target Spacing" (later renamed Target Size (Minimum)), Redundant Entry.
  * 2020-08-04: Added Focus Appearance (Minimum) (later renamed to Focus Appearance) and renamed "Focus Visible (Enhanced)" to "Focus Appearance (Enhanced)".
  * 2020-11-02: Renamed "Dragging" to Dragging Movements.
  * 2020-12-08: Renamed "Hidden Controls" to Visible Controls.
  * 2021-09-21: Added Accessible Authentication (No Exception).
  * 2022-03-22: Added Focus Not Obscured (Minimum).
  * 2022-05-13: Removed Visible Controls.
  * 2022-05-30: Added Focus Not Obscured (Enhanced).
  * 2022-07-15: Removed Page Break Navigation.
  * 2023-06-05: Added privacy and security sections within conformance.



### Acknowledgments

Additional information about participation in the Accessibility Guidelines Working Group (AG WG) can be found on the [Working Group home page](https://www.w3.org/WAI/GL/).
  *[W3C]: World Wide Web Consortium
