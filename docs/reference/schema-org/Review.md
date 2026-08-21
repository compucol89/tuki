> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente: https://schema.org/Review
> Capturado: 2026-08-21

**Note** : You are viewing the development version of [Schema.org](https://schema.org). See [how we work](/docs/howwework.html) for more details. 

[Schema.org](/)

  * [Docs](/docs/documents.html)
  * [Schemas](/docs/schemas.html)
  * [Validate](https://validator.schema.org)
  * [About](/docs/about.html)



[ ](javascript:void\(0\);)

# Review

A Schema.org Type

Usage: [ 1M - 10M Domains Based on monthly aggregations from Google's web index. ](/docs/usage_stats.html) (Google - July 2026) 

[Thing](/Thing "Thing") > [CreativeWork](/CreativeWork "CreativeWork") > [Review](/Review "Review")   


**[more...]**

  * Canonical URL: https://schema.org/Review
  * [Check for open issues.](https://github.com/schemaorg/schemaorg/issues?q=is%3Aissue+is%3Aopen+Review)



A review of an item - for example, of a restaurant, movie, or store.

Property| Expected Type| Description  
---|---|---  
Properties from [Review](/Review "Review")  
` [associatedClaimReview](/associatedClaimReview "associatedClaimReview")` |  [Review](/Review "Review") | An associated [ClaimReview](/ClaimReview), related by specific common content, topic or claim. The expectation is that this property would be most typically used in cases where a single activity is conducting both claim reviews and media reviews, in which case [relatedMediaReview](/relatedMediaReview) would commonly be used on a [ClaimReview](/ClaimReview), while [associatedClaimReview](/associatedClaimReview) would be used on [MediaReview](/MediaReview).   
` [associatedMediaReview](/associatedMediaReview "associatedMediaReview")` |  [Review](/Review "Review") | An associated [MediaReview](/MediaReview), related by specific common content, topic or claim. The expectation is that this property would be most typically used in cases where a single activity is conducting both claim reviews and media reviews, in which case [relatedMediaReview](/relatedMediaReview) would commonly be used on a [ClaimReview](/ClaimReview), while [associatedClaimReview](/associatedClaimReview) would be used on [MediaReview](/MediaReview).   
` [associatedReview](/associatedReview "associatedReview")` |  [Review](/Review "Review") | An associated [Review](/Review).   
` [itemReviewed](/itemReviewed "itemReviewed")` |  [Thing](/Thing "Thing") | The item that is being reviewed/rated.   
` [negativeNotes](/negativeNotes "negativeNotes")` |  [ItemList](/ItemList "ItemList")  or   
[ListItem](/ListItem "ListItem")  or   
[Text](/Text "Text")  or   
[WebContent](/WebContent "WebContent") | Provides negative considerations regarding something, most typically in pro/con lists for reviews (alongside [positiveNotes](/positiveNotes)). For symmetry   
  
In the case of a [Review](/Review), the property describes the [itemReviewed](/itemReviewed) from the perspective of the review; in the case of a [Product](/Product), the product itself is being described. Since product descriptions tend to emphasise positive claims, it may be relatively unusual to find [negativeNotes](/negativeNotes) used in this way. Nevertheless for the sake of symmetry, [negativeNotes](/negativeNotes) can be used on [Product](/Product).  
  
The property values can be expressed either as unstructured text (repeated as necessary), or if ordered, as a list (in which case the most negative is at the beginning of the list).   
` [positiveNotes](/positiveNotes "positiveNotes")` |  [ItemList](/ItemList "ItemList")  or   
[ListItem](/ListItem "ListItem")  or   
[Text](/Text "Text")  or   
[WebContent](/WebContent "WebContent") | Provides positive considerations regarding something, for example product highlights or (alongside [negativeNotes](/negativeNotes)) pro/con lists for reviews.  
  
In the case of a [Review](/Review), the property describes the [itemReviewed](/itemReviewed) from the perspective of the review; in the case of a [Product](/Product), the product itself is being described.  
  
The property values can be expressed either as unstructured text (repeated as necessary), or if ordered, as a list (in which case the most positive is at the beginning of the list).   
` [reviewAspect](/reviewAspect "reviewAspect")` |  [StructuredValue](/StructuredValue "StructuredValue")  or   
[Text](/Text "Text") | This Review or Rating is relevant to this part or facet of the itemReviewed.   
` [reviewBody](/reviewBody "reviewBody")` |  [Text](/Text "Text") | The actual body of the review.   
` [reviewRating](/reviewRating "reviewRating")` |  [Rating](/Rating "Rating") | The rating given in this review. Note that reviews can themselves be rated. The `reviewRating` applies to rating given by the review. The [aggregateRating](/aggregateRating) property applies to the review itself, as a creative work.   
Properties from [CreativeWork](/CreativeWork "CreativeWork")  
` [about](/about "about")` |  [Thing](/Thing "Thing") | The subject matter of an object.   
Inverse property: [subjectOf](/subjectOf "subjectOf")  
` [abstract](/abstract "abstract")` |  [Text](/Text "Text") | An abstract is a short description that summarizes a [CreativeWork](/CreativeWork).   
` [accessMode](/accessMode "accessMode")` |  [Text](/Text "Text") | The human sensory perceptual system or cognitive faculty through which a person may process or perceive the intellectual content of a resource, not including any adaptations of the content (e.g., text alternatives for images). Values should be drawn from the [approved vocabulary](https://www.w3.org/2021/a11y-discov-vocab/latest/#accessMode-vocabulary).   
` [accessModeSufficient](/accessModeSufficient "accessModeSufficient")` |  [ItemList](/ItemList "ItemList") | A list of single or combined access modes that are sufficient to understand all the intellectual content of a resource, including any adaptations. Values should be drawn from the [approved vocabulary](https://www.w3.org/2021/a11y-discov-vocab/latest/#accessModeSufficient-vocabulary).   
` [accessibilityAPI](/accessibilityAPI "accessibilityAPI")` |  [Text](/Text "Text") | Indicates that the resource is compatible with the referenced accessibility API. Values should be drawn from the [approved vocabulary](https://www.w3.org/2021/a11y-discov-vocab/latest/#accessibilityAPI-vocabulary).   
` [accessibilityControl](/accessibilityControl "accessibilityControl")` |  [Text](/Text "Text") | Identifies input methods that are sufficient to fully control the described resource. Values should be drawn from the [approved vocabulary](https://www.w3.org/2021/a11y-discov-vocab/latest/#accessibilityControl-vocabulary).   
` [accessibilityFeature](/accessibilityFeature "accessibilityFeature")` |  [Text](/Text "Text") | Content features of the resource, such as accessible media, alternatives and supported enhancements for accessibility. Values should be drawn from the [approved vocabulary](https://www.w3.org/2021/a11y-discov-vocab/latest/#accessibilityFeature-vocabulary).   
` [accessibilityHazard](/accessibilityHazard "accessibilityHazard")` |  [Text](/Text "Text") | A characteristic of the described resource that is physiologically dangerous to some users. Related to WCAG 2.0 guideline 2.3. Values should be drawn from the [approved vocabulary](https://www.w3.org/2021/a11y-discov-vocab/latest/#accessibilityHazard-vocabulary).   
` [accessibilitySummary](/accessibilitySummary "accessibilitySummary")` |  [Text](/Text "Text") | A human-readable summary of specific accessibility features or deficiencies, consistent with the other accessibility metadata but expressing subtleties such as "short descriptions are present but long descriptions will be needed for non-visual users" or "short descriptions are present and no long descriptions are needed".   
` [accountablePerson](/accountablePerson "accountablePerson")` |  [Person](/Person "Person") | Specifies the Person that is legally accountable for the CreativeWork.   
` [acquireLicensePage](/acquireLicensePage "acquireLicensePage")` |  [CreativeWork](/CreativeWork "CreativeWork")  or   
[URL](/URL "URL") | Indicates a page documenting how licenses can be purchased or otherwise acquired, for the current item.   
` [aggregateRating](/aggregateRating "aggregateRating")` |  [AggregateRating](/AggregateRating "AggregateRating") | The overall rating, based on a collection of reviews or ratings, of the item.   
` [alternativeHeadline](/alternativeHeadline "alternativeHeadline")` |  [Text](/Text "Text") | A secondary title of the CreativeWork.   
` [archivedAt](/archivedAt "archivedAt")` |  [URL](/URL "URL")  or   
[WebPage](/WebPage "WebPage") | Indicates a page or other link involved in archival of a [CreativeWork](/CreativeWork). In the case of [MediaReview](/MediaReview), the items in a [MediaReviewItem](/MediaReviewItem) may often become inaccessible, but be archived by archival, journalistic, activist, or law enforcement organizations. In such cases, the referenced page may not directly publish the content.   
` [assesses](/assesses "assesses")` |  [DefinedTerm](/DefinedTerm "DefinedTerm")  or   
[Text](/Text "Text") | The item being described is intended to assess the competency or learning outcome defined by the referenced term.   
` [associatedMedia](/associatedMedia "associatedMedia")` |  [MediaObject](/MediaObject "MediaObject") | A media object that encodes this CreativeWork. This property is a synonym for encoding.   
` [audience](/audience "audience")` |  [Audience](/Audience "Audience") | An intended audience, i.e. a group for whom something was created. Supersedes [serviceAudience](/serviceAudience "serviceAudience").   
` [audio](/audio "audio")` |  [AudioObject](/AudioObject "AudioObject")  or   
[Clip](/Clip "Clip")  or   
[MusicRecording](/MusicRecording "MusicRecording") | An embedded audio object.   
` [author](/author "author")` |  [Organization](/Organization "Organization")  or   
[Person](/Person "Person") | The author of this content or rating. Please note that author is special in that HTML 5 provides a special mechanism for indicating authorship via the rel tag. That is equivalent to this and may be used interchangeably.   
` [award](/award "award")` |  [Text](/Text "Text") | An award won by or for this item. Supersedes [awards](/awards "awards").   
` [character](/character "character")` |  [Person](/Person "Person") | Fictional person connected with a creative work.   
` [citation](/citation "citation")` |  [CreativeWork](/CreativeWork "CreativeWork")  or   
[Text](/Text "Text") | A citation or reference to another creative work, such as another publication, web page, scholarly article, etc.   
` [comment](/comment "comment")` |  [Comment](/Comment "Comment") | Comments, typically from users.   
` [commentCount](/commentCount "commentCount")` |  [Integer](/Integer "Integer") | The number of comments this CreativeWork (e.g. Article, Question or Answer) has received. This is most applicable to works published in Web sites with commenting system; additional comments may exist elsewhere.   
` [conditionsOfAccess](/conditionsOfAccess "conditionsOfAccess")` |  [Text](/Text "Text") | Conditions that affect the availability of, or method(s) of access to, an item. Typically used for real world items such as an [ArchiveComponent](/ArchiveComponent) held by an [ArchiveOrganization](/ArchiveOrganization). This property is not suitable for use as a general Web access control mechanism. It is expressed only in natural language.  
  
For example "Available by appointment from the Reading Room" or "Accessible only from logged-in accounts ".   
` [contentLocation](/contentLocation "contentLocation")` |  [Place](/Place "Place") | The location depicted or described in the content. For example, the location in a photograph or painting.   
` [contentRating](/contentRating "contentRating")` |  [Rating](/Rating "Rating")  or   
[Text](/Text "Text") | Official rating of a piece of content—for example, 'MPAA PG-13'.   
` [contentReferenceTime](/contentReferenceTime "contentReferenceTime")` |  [DateTime](/DateTime "DateTime") | The specific time described by a creative work, for works (e.g. articles, video objects etc.) that emphasise a particular moment within an Event.   
` [contributor](/contributor "contributor")` |  [Organization](/Organization "Organization")  or   
[Person](/Person "Person") | A secondary contributor to the CreativeWork or Event.   
` [copyrightHolder](/copyrightHolder "copyrightHolder")` |  [Organization](/Organization "Organization")  or   
[Person](/Person "Person") | The party holding the legal copyright to the CreativeWork.   
` [copyrightNotice](/copyrightNotice "copyrightNotice")` |  [Text](/Text "Text") | Text of a notice appropriate for describing the copyright aspects of this Creative Work, ideally indicating the owner of the copyright for the Work.   
` [copyrightYear](/copyrightYear "copyrightYear")` |  [Number](/Number "Number") | The year during which the claimed copyright for the CreativeWork was first asserted.   
` [correction](/correction "correction")` |  [CorrectionComment](/CorrectionComment "CorrectionComment")  or   
[Text](/Text "Text")  or   
[URL](/URL "URL") | Indicates a correction to a [CreativeWork](/CreativeWork), either via a [CorrectionComment](/CorrectionComment), textually or in another document.   
` [countryOfOrigin](/countryOfOrigin "countryOfOrigin")` |  [Country](/Country "Country") | The country of origin of something, including products as well as creative works such as movie and TV content.  
  
In the case of TV and movie, this would be the country of the principle offices of the production company or individual responsible for the movie. For other kinds of [CreativeWork](/CreativeWork) it is difficult to provide fully general guidance, and properties such as [contentLocation](/contentLocation) and [locationCreated](/locationCreated) may be more applicable.  
  
In the case of products, the country of origin of the product. The exact interpretation of this may vary by context and product type, and cannot be fully enumerated here.   
` [creativeWorkStatus](/creativeWorkStatus "creativeWorkStatus")` |  [DefinedTerm](/DefinedTerm "DefinedTerm")  or   
[Text](/Text "Text") | The status of a creative work in terms of its stage in a lifecycle. Example terms include Incomplete, Draft, Published, Obsolete. Some organizations define a set of terms for the stages of their publication lifecycle.   
` [creator](/creator "creator")` |  [Organization](/Organization "Organization")  or   
[Person](/Person "Person") | The creator/author of this CreativeWork. This is the same as the Author property for CreativeWork.   
` [creditText](/creditText "creditText")` |  [Text](/Text "Text") | Text that can be used to credit person(s) and/or organization(s) associated with a published Creative Work.   
` [dateCreated](/dateCreated "dateCreated")` |  [Date](/Date "Date")  or   
[DateTime](/DateTime "DateTime") | The date on which the CreativeWork was created or the item was added to a DataFeed.   
` [dateModified](/dateModified "dateModified")` |  [Date](/Date "Date")  or   
[DateTime](/DateTime "DateTime") | The date on which the CreativeWork was most recently modified or when the item's entry was modified within a DataFeed.   
` [datePublished](/datePublished "datePublished")` |  [Date](/Date "Date")  or   
[DateTime](/DateTime "DateTime") | Date of first publication or broadcast. For example the date a [CreativeWork](/CreativeWork) was broadcast or a [Certification](/Certification) was issued.   
` [digitalSourceType](/digitalSourceType "digitalSourceType")` |  [IPTCDigitalSourceEnumeration](/IPTCDigitalSourceEnumeration "IPTCDigitalSourceEnumeration") | Indicates an IPTCDigitalSourceEnumeration code indicating the nature of the digital source(s) for some [CreativeWork](/CreativeWork).   
` [discussionUrl](/discussionUrl "discussionUrl")` |  [URL](/URL "URL") | A link to the page containing the comments of the CreativeWork.   
` [displayLocation](/displayLocation "displayLocation")` |  [Place](/Place "Place") | The location at which an item can be viewed or experienced in-person.   
` [editEIDR](/editEIDR "editEIDR")` |  [Text](/Text "Text")  or   
[URL](/URL "URL") | An [EIDR](https://eidr.org/) (Entertainment Identifier Registry) [identifier](/identifier) representing a specific edit / edition for a work of film or television.  
  
For example, the motion picture known as "Ghostbusters" whose [titleEIDR](/titleEIDR) is "10.5240/7EC7-228A-510A-053E-CBB8-J" has several edits, e.g. "10.5240/1F2A-E1C5-680A-14C6-E76B-I" and "10.5240/8A35-3BEE-6497-5D12-9E4F-3".  
  
Since schema.org types like [Movie](/Movie) and [TVEpisode](/TVEpisode) can be used for both works and their multiple expressions, it is possible to use [titleEIDR](/titleEIDR) alone (for a general description), or alongside [editEIDR](/editEIDR) for a more edit-specific description.   
` [editor](/editor "editor")` |  [Person](/Person "Person") | Specifies the Person who edited the CreativeWork.   
` [educationalAlignment](/educationalAlignment "educationalAlignment")` |  [AlignmentObject](/AlignmentObject "AlignmentObject") | An alignment to an established educational framework.  
  
This property should not be used where the nature of the alignment can be described using a simple property, for example to express that a resource [teaches](/teaches) or [assesses](/assesses) a competency.   
` [educationalLevel](/educationalLevel "educationalLevel")` |  [DefinedTerm](/DefinedTerm "DefinedTerm")  or   
[Text](/Text "Text")  or   
[URL](/URL "URL") | The level in terms of progression through an educational or training context. Examples of educational levels include 'beginner', 'intermediate' or 'advanced', and formal sets of level indicators.   
` [educationalUse](/educationalUse "educationalUse")` |  [DefinedTerm](/DefinedTerm "DefinedTerm")  or   
[Text](/Text "Text") | The purpose of a work in the context of education; for example, 'assignment', 'group work'.   
` [encoding](/encoding "encoding")` |  [MediaObject](/MediaObject "MediaObject") | A media object that encodes this CreativeWork. This property is a synonym for associatedMedia. Supersedes [encodings](/encodings "encodings").   
Inverse property: [encodesCreativeWork](/encodesCreativeWork "encodesCreativeWork")  
` [encodingFormat](/encodingFormat "encodingFormat")` |  [Text](/Text "Text")  or   
[URL](/URL "URL") | Media type typically expressed using a MIME format (see [IANA site](http://www.iana.org/assignments/media-types/media-types.xhtml) and [MDN reference](https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types)), e.g. application/zip for a SoftwareApplication binary, audio/mpeg for .mp3 etc.  
  
In cases where a [CreativeWork](/CreativeWork) has several media type representations, [encoding](/encoding) can be used to indicate each [MediaObject](/MediaObject) alongside particular [encodingFormat](/encodingFormat) information.  
  
Unregistered or niche encoding and file formats can be indicated instead via the most appropriate URL, e.g. defining Web page or a Wikipedia/Wikidata entry. Supersedes [fileFormat](/fileFormat "fileFormat").   
` [exampleOfWork](/exampleOfWork "exampleOfWork")` |  [CreativeWork](/CreativeWork "CreativeWork") | A creative work that this work is an example/instance/realization/derivation of.   
Inverse property: [workExample](/workExample "workExample")  
` [expires](/expires "expires")` |  [Date](/Date "Date")  or   
[DateTime](/DateTime "DateTime") | Date the content expires and is no longer useful or available. For example a [VideoObject](/VideoObject) or [NewsArticle](/NewsArticle) whose availability or relevance is time-limited, a [ClaimReview](/ClaimReview) fact check whose publisher wants to indicate that it may no longer be relevant (or helpful to highlight) after some date, or a [Certification](/Certification) the validity has expired.   
` [funder](/funder "funder")` |  [Organization](/Organization "Organization")  or   
[Person](/Person "Person") | A person or organization that supports (sponsors) something through some kind of financial contribution.   
` [funding](/funding "funding")` |  [Grant](/Grant "Grant") | A [Grant](/Grant) that directly or indirectly provide funding or sponsorship for this item. See also [ownershipFundingInfo](/ownershipFundingInfo).   
Inverse property: [fundedItem](/fundedItem "fundedItem")  
` [genre](/genre "genre")` |  [DefinedTerm](/DefinedTerm "DefinedTerm")  or   
[Text](/Text "Text")  or   
[URL](/URL "URL") | Genre of the creative work, broadcast channel or group.   
` [hasPart](/hasPart "hasPart")` |  [CreativeWork](/CreativeWork "CreativeWork") | Indicates an item or CreativeWork that is part of this item, or CreativeWork (in some sense).   
Inverse property: [isPartOf](/isPartOf "isPartOf")  
` [headline](/headline "headline")` |  [Text](/Text "Text") | Headline of the article.   
` [inLanguage](/inLanguage "inLanguage")` |  [Language](/Language "Language")  or   
[Text](/Text "Text") | The language of the content or performance or used in an action. Please use one of the language codes from the [IETF BCP 47 standard](http://tools.ietf.org/html/bcp47). See also [availableLanguage](/availableLanguage). Supersedes [language](/language "language").   
` [interactionStatistic](/interactionStatistic "interactionStatistic")` |  [InteractionCounter](/InteractionCounter "InteractionCounter") | The number of interactions for the CreativeWork using the WebSite or SoftwareApplication. The most specific child type of InteractionCounter should be used. Supersedes [interactionCount](/interactionCount "interactionCount").   
` [interactivityType](/interactivityType "interactivityType")` |  [Text](/Text "Text") | The predominant mode of learning supported by the learning resource. Acceptable values are 'active', 'expositive', or 'mixed'.   
` [interpretedAsClaim](/interpretedAsClaim "interpretedAsClaim")` |  [Claim](/Claim "Claim") | Used to indicate a specific claim contained, implied, translated or refined from the content of a [MediaObject](/MediaObject) or other [CreativeWork](/CreativeWork). The interpreting party can be indicated using [claimInterpreter](/claimInterpreter).   
` [isAccessibleForFree](/isAccessibleForFree "isAccessibleForFree")` |  [Boolean](/Boolean "Boolean") | A flag to signal that the item, event, or place is accessible for free. Supersedes [free](/free "free").   
` [isBasedOn](/isBasedOn "isBasedOn")` |  [CreativeWork](/CreativeWork "CreativeWork")  or   
[Product](/Product "Product")  or   
[URL](/URL "URL") | A resource from which this work is derived or from which it is a modification or adaptation. Supersedes [isBasedOnUrl](/isBasedOnUrl "isBasedOnUrl").   
` [isFamilyFriendly](/isFamilyFriendly "isFamilyFriendly")` |  [Boolean](/Boolean "Boolean") | Indicates whether this content is family friendly.   
` [isPartOf](/isPartOf "isPartOf")` |  [CreativeWork](/CreativeWork "CreativeWork")  or   
[URL](/URL "URL") | Indicates an item or CreativeWork that this item, or CreativeWork (in some sense), is part of.   
Inverse property: [hasPart](/hasPart "hasPart")  
` [keywords](/keywords "keywords")` |  [DefinedTerm](/DefinedTerm "DefinedTerm")  or   
[Text](/Text "Text")  or   
[URL](/URL "URL") | Keywords or tags used to describe some item. Multiple textual entries in a keywords list are typically delimited by commas, or by repeating the property.   
` [learningResourceType](/learningResourceType "learningResourceType")` |  [DefinedTerm](/DefinedTerm "DefinedTerm")  or   
[Text](/Text "Text") | The predominant type or kind characterizing the learning resource. For example, 'presentation', 'handout'.   
` [license](/license "license")` |  [CreativeWork](/CreativeWork "CreativeWork")  or   
[URL](/URL "URL") | A license document that applies to this content, typically indicated by URL.   
` [locationCreated](/locationCreated "locationCreated")` |  [Place](/Place "Place") | The location where the CreativeWork was created, which may not be the same as the location depicted in the CreativeWork.   
` [mainEntity](/mainEntity "mainEntity")` |  [Thing](/Thing "Thing") | Indicates the primary entity described in some page or other CreativeWork.   
Inverse property: [mainEntityOfPage](/mainEntityOfPage "mainEntityOfPage")  
` [maintainer](/maintainer "maintainer")` |  [Organization](/Organization "Organization")  or   
[Person](/Person "Person") | A maintainer of a [Dataset](/Dataset), software package ([SoftwareApplication](/SoftwareApplication)), or other [Project](/Project). A maintainer is a [Person](/Person) or [Organization](/Organization) that manages contributions to, and/or publication of, some (typically complex) artifact. It is common for distributions of software and data to be based on "upstream" sources. When [maintainer](/maintainer) is applied to a specific version of something e.g. a particular version or packaging of a [Dataset](/Dataset), it is always possible that the upstream source has a different maintainer. The [isBasedOn](/isBasedOn) property can be used to indicate such relationships between datasets to make the different maintenance roles clear. Similarly in the case of software, a package may have dedicated maintainers working on integration into software distributions such as Ubuntu, as well as upstream maintainers of the underlying work.   
` [material](/material "material")` |  [Product](/Product "Product")  or   
[Text](/Text "Text")  or   
[URL](/URL "URL") | A material that something is made from, e.g. leather, wool, cotton, paper.   
` [materialExtent](/materialExtent "materialExtent")` |  [QuantitativeValue](/QuantitativeValue "QuantitativeValue")  or   
[Text](/Text "Text") | The quantity of the materials being described or an expression of the physical space they occupy.   
` [mentions](/mentions "mentions")` |  [Thing](/Thing "Thing") | Indicates that the CreativeWork contains a reference to, but is not necessarily about a concept.   
` [offers](/offers "offers")` |  [Demand](/Demand "Demand")  or   
[Offer](/Offer "Offer") | An offer to provide this item—for example, an offer to sell a product, rent the DVD of a movie, perform a service, or give away tickets to an event. Use [businessFunction](/businessFunction) to indicate the kind of transaction offered, i.e. sell, lease, etc. This property can also be used to describe a [Demand](/Demand). While this property is listed as expected on a number of common types, it can be used in others. In that case, using a second type, such as Product or a subtype of Product, can clarify the nature of the offer.   
Inverse property: [itemOffered](/itemOffered "itemOffered")  
` [pattern](/pattern "pattern")` |  [DefinedTerm](/DefinedTerm "DefinedTerm")  or   
[Text](/Text "Text") | A pattern that something has, for example 'polka dot', 'striped', 'Canadian flag'. Values are typically expressed as text, although links to controlled value schemes are also supported.   
` [position](/position "position")` |  [Integer](/Integer "Integer")  or   
[Text](/Text "Text") | The position of an item in a series or sequence of items.   
` [producer](/producer "producer")` |  [Organization](/Organization "Organization")  or   
[Person](/Person "Person") | The person or organization who produced the work (e.g. music album, movie, TV/radio series etc.).   
` [provider](/provider "provider")` |  [Organization](/Organization "Organization")  or   
[Person](/Person "Person") | The service provider, service operator, or service performer; the goods producer. Another party (a seller) may offer those services or goods on behalf of the provider. A provider may also serve as the seller. Supersedes [carrier](/carrier "carrier").   
` [publication](/publication "publication")` |  [PublicationEvent](/PublicationEvent "PublicationEvent") | A publication event associated with the item.   
` [publisher](/publisher "publisher")` |  [Organization](/Organization "Organization")  or   
[Person](/Person "Person") | The publisher of the article in question.   
` [publisherImprint](/publisherImprint "publisherImprint")` |  [Organization](/Organization "Organization") | The publishing division which published the comic.   
` [publishingPrinciples](/publishingPrinciples "publishingPrinciples")` |  [CreativeWork](/CreativeWork "CreativeWork")  or   
[URL](/URL "URL") | The publishingPrinciples property indicates (typically via [URL](/URL)) a document describing the editorial principles of an [Organization](/Organization) (or individual, e.g. a [Person](/Person) writing a blog) that relate to their activities as a publisher, e.g. ethics or diversity policies. When applied to a [CreativeWork](/CreativeWork) (e.g. [NewsArticle](/NewsArticle)) the principles are those of the party primarily responsible for the creation of the [CreativeWork](/CreativeWork).  
  
While such policies are most typically expressed in natural language, sometimes related information (e.g. indicating a [funder](/funder)) can be expressed using schema.org terminology.   
` [recordedAt](/recordedAt "recordedAt")` |  [Event](/Event "Event") | The Event where the CreativeWork was recorded. The CreativeWork may capture all or part of the event.   
Inverse property: [recordedIn](/recordedIn "recordedIn")  
` [releasedEvent](/releasedEvent "releasedEvent")` |  [PublicationEvent](/PublicationEvent "PublicationEvent") | The place and time the release was issued, expressed as a PublicationEvent.   
` [review](/review "review")` |  [Review](/Review "Review") | A review of the item. Supersedes [reviews](/reviews "reviews").   
` [schemaVersion](/schemaVersion "schemaVersion")` |  [Text](/Text "Text")  or   
[URL](/URL "URL") | Indicates (by URL or string) a particular version of a schema used in some CreativeWork. This property was created primarily to indicate the use of a specific schema.org release, e.g. `10.0` as a simple string, or more explicitly via URL, `https://schema.org/docs/releases.html#v10.0`. There may be situations in which other schemas might usefully be referenced this way, e.g. `http://dublincore.org/specifications/dublin-core/dces/1999-07-02/` but this has not been carefully explored in the community.   
` [sdDatePublished](/sdDatePublished "sdDatePublished")` |  [Date](/Date "Date") | Indicates the date on which the current structured data was generated / published. Typically used alongside [sdPublisher](/sdPublisher).   
` [sdLicense](/sdLicense "sdLicense")` |  [CreativeWork](/CreativeWork "CreativeWork")  or   
[URL](/URL "URL") | A license document that applies to this structured data, typically indicated by URL.   
` [sdPublisher](/sdPublisher "sdPublisher")` |  [Organization](/Organization "Organization")  or   
[Person](/Person "Person") | Indicates the party responsible for generating and publishing the current structured data markup, typically in cases where the structured data is derived automatically from existing published content but published on a different site. For example, student projects and open data initiatives often re-publish existing content with more explicitly structured metadata. The [sdPublisher](/sdPublisher) property helps make such practices more explicit.   
` [size](/size "size")` |  [DefinedTerm](/DefinedTerm "DefinedTerm")  or   
[QuantitativeValue](/QuantitativeValue "QuantitativeValue")  or   
[SizeSpecification](/SizeSpecification "SizeSpecification")  or   
[Text](/Text "Text") | A standardized size of a product or creative work, specified either through a simple textual string (for example 'XL', '32Wx34L'), a QuantitativeValue with a unitCode, or a comprehensive and structured [SizeSpecification](/SizeSpecification); in other cases, the [width](/width), [height](/height), [depth](/depth) and [weight](/weight) properties may be more applicable.   
` [sourceOrganization](/sourceOrganization "sourceOrganization")` |  [Organization](/Organization "Organization") | The Organization on whose behalf the creator was working.   
` [spatial](/spatial "spatial")` |  [Place](/Place "Place") | The "spatial" property can be used in cases when more specific properties (e.g. [locationCreated](/locationCreated), [spatialCoverage](/spatialCoverage), [contentLocation](/contentLocation)) are not known to be appropriate.   
` [spatialCoverage](/spatialCoverage "spatialCoverage")` |  [Place](/Place "Place") | The spatialCoverage of a CreativeWork indicates the place(s) which are the focus of the content. It is a subproperty of contentLocation intended primarily for more technical and detailed materials. For example with a Dataset, it indicates areas that the dataset describes: a dataset of New York weather would have spatialCoverage which was the place: the state of New York.   
` [sponsor](/sponsor "sponsor")` |  [Organization](/Organization "Organization")  or   
[Person](/Person "Person") | A person or organization that supports a thing through a pledge, promise, or financial contribution. E.g. a sponsor of a Medical Study or a corporate sponsor of an event.   
` [teaches](/teaches "teaches")` |  [DefinedTerm](/DefinedTerm "DefinedTerm")  or   
[Text](/Text "Text") | The item being described is intended to help a person learn the competency or learning outcome defined by the referenced term.   
` [temporal](/temporal "temporal")` |  [DateTime](/DateTime "DateTime")  or   
[Text](/Text "Text") | The "temporal" property can be used in cases where more specific properties (e.g. [temporalCoverage](/temporalCoverage), [dateCreated](/dateCreated), [dateModified](/dateModified), [datePublished](/datePublished)) are not known to be appropriate.   
` [temporalCoverage](/temporalCoverage "temporalCoverage")` |  [DateTime](/DateTime "DateTime")  or   
[Text](/Text "Text")  or   
[URL](/URL "URL") | The temporalCoverage of a CreativeWork indicates the period that the content applies to, i.e. that it describes, either as a DateTime or as a textual string indicating a time period in [ISO 8601 time interval format](https://en.wikipedia.org/wiki/ISO_8601#Time_intervals). In the case of a Dataset it will typically indicate the relevant time period in a precise notation (e.g. for a 2011 census dataset, the year 2011 would be written "2011/2012"). Other forms of content, e.g. ScholarlyArticle, Book, TVSeries or TVEpisode, may indicate their temporalCoverage in broader terms - textually or via well-known URL. Written works such as books may sometimes have precise temporal coverage too, e.g. a work set in 1939 - 1945 can be indicated in ISO 8601 interval format format via "1939/1945".  
  
Open-ended date ranges can be written with ".." in place of the end date. For example, "2015-11/.." indicates a range beginning in November 2015 and with no specified final date. This is tentative and might be updated in future when ISO 8601 is officially updated. Supersedes [datasetTimeInterval](/datasetTimeInterval "datasetTimeInterval").   
` [text](/text "text")` |  [Text](/Text "Text") | The textual content of this CreativeWork.   
` [thumbnail](/thumbnail "thumbnail")` |  [ImageObject](/ImageObject "ImageObject") | Thumbnail image for an image or video.   
` [thumbnailUrl](/thumbnailUrl "thumbnailUrl")` |  [URL](/URL "URL") | A thumbnail image relevant to the Thing.   
` [timeRequired](/timeRequired "timeRequired")` |  [Duration](/Duration "Duration") | Approximate or typical time it usually takes to work with or through the content of this work for the typical or target audience.   
` [translationOfWork](/translationOfWork "translationOfWork")` |  [CreativeWork](/CreativeWork "CreativeWork") | The work that this work has been translated from. E.g. 物种起源 is a translationOf “On the Origin of Species”.   
Inverse property: [workTranslation](/workTranslation "workTranslation")  
` [translator](/translator "translator")` |  [Organization](/Organization "Organization")  or   
[Person](/Person "Person") | Organization or person who adapts a creative work to different languages, regional differences and technical requirements of a target market, or that translates during some event.   
` [typicalAgeRange](/typicalAgeRange "typicalAgeRange")` |  [Text](/Text "Text") | The typical expected age range, e.g. '7-9', '11-'.   
` [usageInfo](/usageInfo "usageInfo")` |  [CreativeWork](/CreativeWork "CreativeWork")  or   
[URL](/URL "URL") | The schema.org [usageInfo](/usageInfo) property indicates further information about a [CreativeWork](/CreativeWork). This property is applicable both to works that are freely available and to those that require payment or other transactions. It can reference additional information, e.g. community expectations on preferred linking and citation conventions, as well as purchasing details. For something that can be commercially licensed, usageInfo can provide detailed, resource-specific information about licensing options.  
  
This property can be used alongside the license property which indicates license(s) applicable to some piece of content. The usageInfo property can provide information about other licensing options, e.g. acquiring commercial usage rights for an image that is also available under non-commercial creative commons licenses.   
` [version](/version "version")` |  [Number](/Number "Number")  or   
[Text](/Text "Text") | The version of the CreativeWork embodied by a specified resource.   
` [video](/video "video")` |  [Clip](/Clip "Clip")  or   
[VideoObject](/VideoObject "VideoObject") | An embedded video object.   
` [wordCount](/wordCount "wordCount")` |  [Integer](/Integer "Integer") | The number of words in the text of the CreativeWork such as an Article, Book, etc.   
` [workExample](/workExample "workExample")` |  [CreativeWork](/CreativeWork "CreativeWork") | Example/instance/realization/derivation of the concept of this creative work. E.g. the paperback edition, first edition, or e-book.   
Inverse property: [exampleOfWork](/exampleOfWork "exampleOfWork")  
` [workTranslation](/workTranslation "workTranslation")` |  [CreativeWork](/CreativeWork "CreativeWork") | A work that is a translation of the content of this work. E.g. 西遊記 has an English workTranslation “Journey to the West”, a German workTranslation “Monkeys Pilgerfahrt” and a Vietnamese translation Tây du ký bình khảo.   
Inverse property: [translationOfWork](/translationOfWork "translationOfWork")  
Properties from [Thing](/Thing "Thing")  
` [additionalType](/additionalType "additionalType")` |  [Text](/Text "Text")  or   
[URL](/URL "URL") | An additional type for the item, typically used for adding more specific types from external vocabularies in microdata syntax. This is a relationship between something and a class that the thing is in. Typically the value is a URI-identified RDF class, and in this case corresponds to the use of rdf:type in RDF. Text values can be used sparingly, for cases where useful information can be added without their being an appropriate schema to reference. In the case of text values, the class label should follow the schema.org [style guide](https://schema.org/docs/styleguide.html).   
` [alternateName](/alternateName "alternateName")` |  [Text](/Text "Text") | An alias for the item.   
` [description](/description "description")` |  [Text](/Text "Text")  or   
[TextObject](/TextObject "TextObject") | A description of the item.   
` [disambiguatingDescription](/disambiguatingDescription "disambiguatingDescription")` |  [Text](/Text "Text") | A sub property of description. A short description of the item used to disambiguate from other, similar items. Information from other properties (in particular, name) may be necessary for the description to be useful for disambiguation.   
` [identifier](/identifier "identifier")` |  [PropertyValue](/PropertyValue "PropertyValue")  or   
[Text](/Text "Text")  or   
[URL](/URL "URL") | The identifier property represents any kind of identifier for any kind of [Thing](/Thing), such as ISBNs, GTIN codes, UUIDs etc. Schema.org provides dedicated properties for representing many of these, either as textual strings or as URL (URI) links. See [background notes](/docs/datamodel.html#identifierBg) for more details.   
` [image](/image "image")` |  [ImageObject](/ImageObject "ImageObject")  or   
[URL](/URL "URL") | An image of the item. This can be a [URL](/URL) or a fully described [ImageObject](/ImageObject).   
` [mainEntityOfPage](/mainEntityOfPage "mainEntityOfPage")` |  [CreativeWork](/CreativeWork "CreativeWork")  or   
[URL](/URL "URL") | Indicates a page (or other CreativeWork) for which this thing is the main entity being described. See [background notes](/docs/datamodel.html#mainEntityBackground) for details.   
Inverse property: [mainEntity](/mainEntity "mainEntity")  
` [name](/name "name")` |  [Text](/Text "Text") | The name of the item.   
` [owner](/owner "owner")` |  [Organization](/Organization "Organization")  or   
[Person](/Person "Person") | A person or organization who owns this Thing.   
Inverse property: [owns](/owns "owns")  
` [potentialAction](/potentialAction "potentialAction")` |  [Action](/Action "Action") | Indicates a potential Action, which describes an idealized action in which this thing would play an 'object' role.   
` [sameAs](/sameAs "sameAs")` |  [URL](/URL "URL") | URL of a reference Web page that unambiguously indicates the item's identity. E.g. the URL of the item's Wikipedia page, Wikidata entry, or official website.   
` [subjectOf](/subjectOf "subjectOf")` |  [CreativeWork](/CreativeWork "CreativeWork")  or   
[Event](/Event "Event") | A CreativeWork or Event about this Thing.   
Inverse property: [about](/about "about")  
` [url](/url "url")` |  [URL](/URL "URL") | URL of the item.   
  


Instances of [Review](/Review "Review") may appear as a value for the following properties

Property| On Types| Description  
---|---|---  
[associatedClaimReview](/associatedClaimReview "associatedClaimReview") |  [Review](/Review "Review") | An associated [ClaimReview](/ClaimReview), related by specific common content, topic or claim. The expectation is that this property would be most typically used in cases where a single activity is conducting both claim reviews and media reviews, in which case [relatedMediaReview](/relatedMediaReview) would commonly be used on a [ClaimReview](/ClaimReview), while [associatedClaimReview](/associatedClaimReview) would be used on [MediaReview](/MediaReview).   
[associatedMediaReview](/associatedMediaReview "associatedMediaReview") |  [Review](/Review "Review") | An associated [MediaReview](/MediaReview), related by specific common content, topic or claim. The expectation is that this property would be most typically used in cases where a single activity is conducting both claim reviews and media reviews, in which case [relatedMediaReview](/relatedMediaReview) would commonly be used on a [ClaimReview](/ClaimReview), while [associatedClaimReview](/associatedClaimReview) would be used on [MediaReview](/MediaReview).   
[associatedReview](/associatedReview "associatedReview") |  [Review](/Review "Review") | An associated [Review](/Review).   
[resultReview](/resultReview "resultReview") |  [ReviewAction](/ReviewAction "ReviewAction") | A sub property of result. The review that resulted in the performing of the action.   
[review](/review "review") |  [Brand](/Brand "Brand")  or   
[CreativeWork](/CreativeWork "CreativeWork")  or   
[Event](/Event "Event")  or   
[Offer](/Offer "Offer")  or   
[Organization](/Organization "Organization")  or   
[Place](/Place "Place")  or   
[Product](/Product "Product")  or   
[Service](/Service "Service") | A review of the item.   
[reviews](/reviews "reviews") |  [CreativeWork](/CreativeWork "CreativeWork")  or   
[Offer](/Offer "Offer")  or   
[Organization](/Organization "Organization")  or   
[Place](/Place "Place")  or   
[Product](/Product "Product") | Review of the item.   
  


#### More specific Types

  * [ClaimReview](/ClaimReview "ClaimReview")
  * [CriticReview](/CriticReview "CriticReview")
  * [EmployerReview](/EmployerReview "EmployerReview")
  * [MediaReview](/MediaReview "MediaReview")
  * [Recommendation](/Recommendation "Recommendation")
  * [UserReview](/UserReview "UserReview")



### Examples

Example 1

Copied

No Markup Microdata RDFa JSON-LD Structure

Example notes or example HTML without markup.
    
    
    Kenmore White 17" Microwave
    <img src="kenmore-microwave-17in.jpg" alt='Kenmore 17" Microwave' />
    Rated 3.5/5 based on 11 customer reviews
    
    $55.00
    In stock
    
    Product description:
    0.7 cubic feet countertop microwave. Has six preset cooking categories and
     convenience features like Add-A-Minute and Child Lock.
    
    Customer reviews:
    
    Not a happy camper - by Ellie, April 1, 2011
    1/5 stars
    The lamp burned out and now I have to replace it.
    
     Value purchase - by Lucas, March 25, 2011
    4/5 stars
    Great microwave for the price. It is small and fits in my apartment.
    ...
    

Example encoded as [Microdata](https://en.wikipedia.org/wiki/Microdata_\(HTML\)) embedded in HTML.
    
    
    <div itemscope itemtype="https://schema.org/Product">
      <span itemprop="name">Kenmore White 17" Microwave</span>
      <img itemprop="image" src="kenmore-microwave-17in.jpg" alt='Kenmore 17" Microwave' />
      <div itemprop="aggregateRating"
        itemscope itemtype="https://schema.org/AggregateRating">
       Rated <span itemprop="ratingValue">3.5</span>/5
       based on <span itemprop="reviewCount">11</span> customer reviews
      </div>
    
      <div itemprop="offers" itemscope itemtype="https://schema.org/Offer">
    
        <!--price is 1000, a number, with locale-specific thousands separator
        and decimal mark, and the $ character is marked up with the
        machine-readable code "USD" -->
        <span itemprop="priceCurrency" content="USD">$</span><span
              itemprop="price" content="1000.00">1,000.00</span>
    
        <link itemprop="availability" href="https://schema.org/InStock" />In stock
      </div>
    
      Product description:
      <span itemprop="description">0.7 cubic feet countertop microwave.
      Has six preset cooking categories and convenience features like
      Add-A-Minute and Child Lock.</span>
    
      Customer reviews:
    
      <div itemprop="review" itemscope itemtype="https://schema.org/Review">
        <span itemprop="name">Not a happy camper</span> -
        by <span itemprop="author">Ellie</span>,
        <meta itemprop="datePublished" content="2011-04-01">April 1, 2011
        <div itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
          <meta itemprop="worstRating" content = "1">
          <span itemprop="ratingValue">1</span>/
          <span itemprop="bestRating">5</span>stars
        </div>
        <span itemprop="reviewBody">The lamp burned out and now I have to replace
        it. </span>
      </div>
    
      <div itemprop="review" itemscope itemtype="https://schema.org/Review">
        <span itemprop="name">Value purchase</span> -
        by <span itemprop="author">Lucas</span>,
        <meta itemprop="datePublished" content="2011-03-25">March 25, 2011
        <div itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
          <meta itemprop="worstRating" content = "1"/>
          <span itemprop="ratingValue">4</span>/
          <span itemprop="bestRating">5</span>stars
        </div>
        <span itemprop="reviewBody">Great microwave for the price. It is small and
        fits in my apartment.</span>
      </div>
      ...
    </div>
    

Example encoded as [RDFa](https://en.wikipedia.org/wiki/RDFa) embedded in HTML.
    
    
    <div vocab="https://schema.org/" typeof="Product">
      <span property="name">Kenmore White 17" Microwave</span>
      <img property="image" src="kenmore-microwave-17in.jpg" alt='Kenmore 17" Microwave' />
      <div property="aggregateRating"
         typeof="AggregateRating">
       Rated <span property="ratingValue">3.5</span>/5
       based on <span property="reviewCount">11</span> customer reviews
      </div>
    
      <div property="offers" typeof="Offer">
        <!--price is 1000, a number, with locale-specific thousands separator
            and decimal mark, and the $ character is marked up with the
            machine-readable code "USD" -->
        <span property="priceCurrency" content="USD">$</span><span
          property="price" content="1000.00">1,000.00</span>
        <link property="availability" href="https://schema.org/InStock" />In stock
      </div>
    
      Product description:
      <span property="description">0.7 cubic feet countertop microwave.
      Has six preset cooking categories and convenience features like
      Add-A-Minute and Child Lock.</span>
    
      Customer reviews:
    
      <div property="review" typeof="Review">
        <span property="name">Not a happy camper</span> -
        by <span property="author">Ellie</span>,
        <meta property="datePublished" content="2011-04-01">April 1, 2011
        <div property="reviewRating" typeof="Rating">
          <meta property="worstRating" content = "1">
          <span property="ratingValue">1</span>/
          <span property="bestRating">5</span>stars
        </div>
        <span property="reviewBody">The lamp burned out and now I have to replace
        it. </span>
      </div>
    
      <div property="review" typeof="Review">
        <span property="name">Value purchase</span> -
        by <span property="author">Lucas</span>,
        <meta property="datePublished" content="2011-03-25">March 25, 2011
        <div property="reviewRating" typeof="Rating">
          <meta property="worstRating" content = "1"/>
          <span property="ratingValue">4</span>/
          <span property="bestRating">5</span>stars
        </div>
        <span property="reviewBody">Great microwave for the price. It is small and
        fits in my apartment.</span>
      </div>
      ...
    </div>
    

Example encoded as [JSON-LD](https://en.wikipedia.org/wiki/JSON-LD) in a HTML script tag.
    
    
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Product",
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "3.5",
        "reviewCount": "11"
      },
      "description": "0.7 cubic feet countertop microwave. Has six preset cooking categories and convenience features like Add-A-Minute and Child Lock.",
      "name": "Kenmore White 17\" Microwave",
      "image": "kenmore-microwave-17in.jpg",
      "offers": {
        "@type": "Offer",
        "availability": "https://schema.org/InStock",
        "price": "55.00",
        "priceCurrency": "USD"
      },
      "review": [
        {
          "@type": "Review",
          "author": "Ellie",
          "datePublished": "2011-04-01",
          "reviewBody": "The lamp burned out and now I have to replace it.",
          "name": "Not a happy camper",
          "reviewRating": {
            "@type": "Rating",
            "bestRating": "5",
            "ratingValue": "1",
            "worstRating": "1"
          }
        },
        {
          "@type": "Review",
          "author": "Lucas",
          "datePublished": "2011-03-25",
          "reviewBody": "Great microwave for the price. It is small and fits in my apartment.",
          "name": "Value purchase",
          "reviewRating": {
            "@type": "Rating",
            "bestRating": "5",
            "ratingValue": "4",
            "worstRating": "1"
          }
        }
      ]
    }
    </script>
    

Structured representation of the JSON-LD example.

{ "@context": "https://schema.org", "@type": "Product", "aggregateRating": { "@type": "AggregateRating", "ratingValue": "3.5", "reviewCount": "11" }, "description": "0.7 cubic feet countertop microwave. Has six preset cooking categories and convenience features like Add-A-Minute and Child Lock.", "name": "Kenmore White 17\" Microwave", "image": "kenmore-microwave-17in.jpg", "offers": { "@type": "Offer", "availability": "https://schema.org/InStock", "price": "55.00", "priceCurrency": "USD" }, "review": [ { "@type": "Review", "author": "Ellie", "datePublished": "2011-04-01", "reviewBody": "The lamp burned out and now I have to replace it.", "name": "Not a happy camper", "reviewRating": { "@type": "Rating", "bestRating": "5", "ratingValue": "1", "worstRating": "1" } }, { "@type": "Review", "author": "Lucas", "datePublished": "2011-03-25", "reviewBody": "Great microwave for the price. It is small and fits in my apartment.", "name": "Value purchase", "reviewRating": { "@type": "Rating", "bestRating": "5", "ratingValue": "4", "worstRating": "1" } } ] }

Example 2

Copied

No Markup JSON-LD Structure

Example notes or example HTML without markup.
    
    
    See JSON example.
    

Example encoded as [JSON-LD](https://en.wikipedia.org/wiki/JSON-LD) in a HTML script tag.
    
    
    <script type="application/ld+json">
    {
      "@context": "https://schema.org/",
      "@type": "Review",
      "reviewBody": "The restaurant has great ambiance.",
      "itemReviewed": {
        "@type": "Restaurant",
        "name": "Fine Dining Establishment"
      },
      "reviewRating": {
        "@type": "Rating",
        "ratingValue": 5,
        "worstRating": 1,
        "bestRating": 5,
        "reviewAspect": "Ambiance"
      }
    }
    </script>
    

Structured representation of the JSON-LD example.

{ "@context": "https://schema.org/", "@type": "Review", "reviewBody": "The restaurant has great ambiance.", "itemReviewed": { "@type": "Restaurant", "name": "Fine Dining Establishment" }, "reviewRating": { "@type": "Rating", "ratingValue": 5, "worstRating": 1, "bestRating": 5, "reviewAspect": "Ambiance" } }

Example 3

Copied

No Markup Microdata RDFa JSON-LD Structure

Example notes or example HTML without markup.
    
    
    <a href="category/books.html">Books</a> >
     <a href="category/books-literature.html">Literature &amp; Fiction</a> >
     <a href="category/books-classics">Classics</a>
    
    <img src="catcher-in-the-rye-book-cover.jpg"
      alt="cover art: red horse, city in background"/>
    The Catcher in the Rye - Mass Market Paperback
    by <a href="/author/jd_salinger.html">J.D. Salinger</a>
    4 stars - 3077 reviews
    
    Price: $6.99
    In Stock
    
    Product details
    224 pages
    Publisher: Little, Brown, and Company - May 1, 1991
    Language: English
    ISBN-10: 0316769487
    
    Reviews:
    
    5 stars - <b>"A masterpiece of literature" </b>
    by John Doe. Written on May 4, 2006
    I really enjoyed this book. It captures the essential challenge people face
    as they try make sense of their lives and grow to adulthood.
    
    4 stars - <b>"love it LOLOL111!" </b>
    by Bob Smith, Written on June 15, 2006
    Catcher in the Rye is a fun book. It's a good book to read.
    

Example encoded as [Microdata](https://en.wikipedia.org/wiki/Microdata_\(HTML\)) embedded in HTML.
    
    
    <body itemscope itemtype="https://schema.org/WebPage">
    ...
    <div itemprop="breadcrumb">
      <a href="category/books.html">Books</a> >
      <a href="category/books-literature.html">Literature &amp; Fiction</a> >
      <a href="category/books-classics">Classics</a>
    </div>
    
    <div itemprop="mainEntity" itemscope itemtype="https://schema.org/Book">
    
    <img itemprop="image" src="catcher-in-the-rye-book-cover.jpg"
         alt="cover art: red horse, city in background"/>
    <span itemprop="name">The Catcher in the Rye</span> -
     <link itemprop="bookFormat" href="https://schema.org/Paperback">Mass Market Paperback
    by <a itemprop="author" href="/author/jd_salinger.html">J.D. Salinger</a>
    
    <div itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
      <span itemprop="ratingValue">4</span> stars -
      <span itemprop="reviewCount">3077</span> reviews
    </div>
    
    <div itemprop="offers" itemscope itemtype="https://schema.org/Offer">
      Price: $<span itemprop="price">6.99</span>
      <meta itemprop="priceCurrency" content="USD" />
      <link itemprop="availability" href="https://schema.org/InStock">In Stock
    </div>
    
    Product details
    <span itemprop="numberOfPages">224</span> pages
    Publisher: <span itemprop="publisher">Little, Brown, and Company</span> -
     <meta itemprop="datePublished" content="1991-05-01">May 1, 1991
    Language: <span itemprop="inLanguage">English</span>
    ISBN-10: <span itemprop="isbn">0316769487</span>
    
    Reviews:
    
    <div itemprop="review" itemscope itemtype="https://schema.org/Review">
      <span itemprop="reviewRating">5</span> stars -
      <b>"<span itemprop="name">A masterpiece of literature</span>"</b>
      by <span itemprop="author">John Doe</span>,
      Written on <meta itemprop="datePublished" content="2006-05-04">May 4, 2006
      <span itemprop="reviewBody">I really enjoyed this book. It captures the essential
      challenge people face as they try make sense of their lives and grow to adulthood.</span>
    </div>
    
    <div itemprop="review" itemscope itemtype="https://schema.org/Review">
      <span itemprop="reviewRating">4</span> stars -
      <b>"<span itemprop="name">A good read.</span>" </b>
      by <span itemprop="author">Bob Smith</span>,
      Written on <meta itemprop="datePublished" content="2006-06-15">June 15, 2006
      <span itemprop="reviewBody">Catcher in the Rye is a fun book. It's a good book to read.</span>
    </div>
    
    </div>
    ...
    </body>
    

Example encoded as [RDFa](https://en.wikipedia.org/wiki/RDFa) embedded in HTML.
    
    
    <body vocab="https://schema.org/" typeof="WebPage">
    ...
    <div property="breadcrumb">
      <a href="category/books.html">Books</a> >
      <a href="category/books-literature.html">Literature &amp; Fiction</a> >
      <a href="category/books-classics">Classics</a>
    </div>
    
    <div property="mainEntity" typeof="Book">
    
    <img property="image" src="catcher-in-the-rye-book-cover.jpg"
        alt="cover art: red horse, city in background"/>
    <span property="name">The Catcher in the Rye</span> -
     <link property="bookFormat" href="https://schema.org/Paperback">Mass Market Paperback
    by <a property="author" href="/author/jd_salinger.html">J.D. Salinger</a>
    
    <div property="aggregateRating" typeof="AggregateRating">
      <span property="ratingValue">4</span> stars -
      <span property="reviewCount">3077</span> reviews
    </div>
    
    <div property="offers" typeof="Offer">
      Price: $<span property="price">6.99</span>
      <meta property="priceCurrency" content="USD" />
      <link property="availability" href="https://schema.org/InStock">In Stock
    </div>
    
    Product details
    <span property="numberOfPages">224</span> pages
    Publisher: <span property="publisher">Little, Brown, and Company</span> -
     <meta property="datePublished" content="1991-05-01">May 1, 1991
    Language: <span property="inLanguage">English</span>
    ISBN-10: <span property="isbn">0316769487</span>
    
    Reviews:
    
    <div property="review" typeof="Review">
      <span property="reviewRating">5</span> stars -
      <b>"<span property="name">A masterpiece of literature</span>"</b>
      by <span property="author">John Doe</span>,
      Written on <meta property="datePublished" content="2006-05-04">May 4, 2006
      <span property="reviewBody">I really enjoyed this book. It captures the essential
      challenge people face as they try make sense of their lives and grow to adulthood.</span>
    </div>
    
    <div property="review" typeof="Review">
      <span property="reviewRating">4</span> stars -
      <b>"<span property="name">A good read.</span>" </b>
      by <span property="author">Bob Smith</span>,
      Written on <meta property="datePublished" content="2006-06-15">June 15, 2006
      <span property="reviewBody">Catcher in the Rye is a fun book. It's a good book to read.</span>
    </div>
    
    </div>
    ...
    </body>
    

Example encoded as [JSON-LD](https://en.wikipedia.org/wiki/JSON-LD) in a HTML script tag.
    
    
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebPage",
      "breadcrumb": "Books > Literature & Fiction > Classics",
      "mainEntity":{
        "@type": "Book",
        "author": "/author/jd_salinger.html",
        "bookFormat": "https://schema.org/Paperback",
        "datePublished": "1991-05-01",
        "image": "catcher-in-the-rye-book-cover.jpg",
        "inLanguage": "English",
        "isbn": "0316769487",
        "name": "The Catcher in the Rye",
        "numberOfPages": "224",
        "offers": {
          "@type": "Offer",
          "availability": "https://schema.org/InStock",
          "price": "6.99",
          "priceCurrency": "USD"
        },
        "publisher": "Little, Brown, and Company",
        "aggregateRating": {
          "@type": "AggregateRating",
          "ratingValue": "4",
          "reviewCount": "3077"
        },
        "review": [
          {
            "@type": "Review",
            "author": "John Doe",
            "datePublished": "2006-05-04",
            "name": "A masterpiece of literature",
            "reviewBody": "I really enjoyed this book. It captures the essential challenge people face as they try make sense of their lives and grow to adulthood.",
            "reviewRating": {
                "@type": "Rating",
                "ratingValue": "5"
               }
          },
          {
            "@type": "Review",
            "author": "Bob Smith",
            "datePublished": "2006-06-15",
            "name": "A good read.",
            "reviewBody": "Catcher in the Rye is a fun book. It's a good book to read.",
            "reviewRating": "4"
          }
        ]
        }
    }
    </script>
    

Structured representation of the JSON-LD example.

{ "@context": "https://schema.org", "@type": "WebPage", "breadcrumb": "Books > Literature & Fiction > Classics", "mainEntity":{ "@type": "Book", "author": "/author/jd_salinger.html", "bookFormat": "https://schema.org/Paperback", "datePublished": "1991-05-01", "image": "catcher-in-the-rye-book-cover.jpg", "inLanguage": "English", "isbn": "0316769487", "name": "The Catcher in the Rye", "numberOfPages": "224", "offers": { "@type": "Offer", "availability": "https://schema.org/InStock", "price": "6.99", "priceCurrency": "USD" }, "publisher": "Little, Brown, and Company", "aggregateRating": { "@type": "AggregateRating", "ratingValue": "4", "reviewCount": "3077" }, "review": [ { "@type": "Review", "author": "John Doe", "datePublished": "2006-05-04", "name": "A masterpiece of literature", "reviewBody": "I really enjoyed this book. It captures the essential challenge people face as they try make sense of their lives and grow to adulthood.", "reviewRating": { "@type": "Rating", "ratingValue": "5" } }, { "@type": "Review", "author": "Bob Smith", "datePublished": "2006-06-15", "name": "A good read.", "reviewBody": "Catcher in the Rye is a fun book. It's a good book to read.", "reviewRating": "4" } ] } }

[Terms and conditions](/docs/terms.html)

• Schema.org • V30.0 | 2026-03-19 
  *[WCAG]: Web Content Accessibility Guidelines
  *[WAI]: Web Accessibility Initiative
  *[ARIA]: Accessible Rich Internet Applications
  *[attr]: attribute
  *[HTML]: HyperText Markup Language
  *[RFS]: Responsive font sizes
