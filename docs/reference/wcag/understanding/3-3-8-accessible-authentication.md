> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente publica: https://raw.githubusercontent.com/w3c/wcag/main/understanding/22/accessible-authentication-minimum.html
> Espejo oficial: w3c/wcag (GitHub)
> Capturado: 2026-08-21

# Understanding Accessible Authentication (Minimum)

## In brief

Goal
    Make logins possible with less mental effort.
What to do
    Don't make people solve, recall, or transcribe something to log in.
Why it's important
    Some people with cognitive disabilities cannot solve puzzles, memorize a username and password, or retype one-time passcodes.

## Intent of Accessible Authentication (Minimum)

The purpose of this success criterion is to ensure there is an accessible, easy-to-use, and secure method for users to authenticate when logging into an existing account. As the most prevalent form of authentication, websites commonly rely on usernames and passwords to log in. However, memorizing a username and password places a very high or impossible burden upon people with certain cognitive disabilities, as do additional steps often added to authentication processes. For instance, the need to transcribe a one-time verification code or requiring a puzzle to be solved.

While websites can use the recognition of objects or of non-text content provided by the user to meet this success criterion, such techniques do not fully support the cognitive accessibility community and should be avoided if possible. Refer to [Accessible Authentication (Enhanced)](accessible-authentication-enhanced) for guidance to be more inclusive and accessible.

This success criterion is focused on authentication of existing users. It does _not_ cover creation of a username or initiation of an account. For many websites, establishing an initial username and credentials may not differ greatly from logging in with that username. The techniques used to satisfy this criterion (particularly allowing pasting into inputs and not relying on transcription) can also reduce the cognitive burden in account creation. However, the focus of the success criterion is on reducing the ongoing need for users to recall previously supplied information each time they log in or otherwise authenticate to an account.

### Cognitive Function Tests

Remembering a site-specific password is a cognitive function test. Such tests are known to be problematic for many people with cognitive disabilities. Whether it is remembering random strings of characters, or a pattern gesture to perform on a touchscreen, cognitive function tests will exclude some people. When a cognitive function test is used, at least one other authentication method must be available which is not a cognitive function test.

Some CAPTCHA systems have an audio alternative of the visible text. If the user needs to transcribe this audio, it cannot be used to meet the Alternative exception.

A CAPTCHA with distorted text, asking users to type in the characters, with an "Audio alternative" button below the text field.

If there is more than one step in the authentication process, such as with multi-factor authentication, all steps need to comply with this success criterion to pass. There needs to be a path through authentication that does not rely on cognitive function tests.

Being able to recover or change the email and password is an important part of authentication. If the user is authenticating with alternative information in order to recover their account, there needs to be a method that is not a cognitive function test.

Many organizations are required to use 2-factor authentication that combines independent sources to confirm a user's identity. These sources can consist of combining authentication through:

  * knowledge (e.g., password, letters in a passphrase or memorized swipe path);
  * possession (e.g., a verification code generated or received on a device, or scanning of a QR code on an external device);
  * biometrics (e.g., fingerprint scanning, facial recognition or keystroke dynamics).



Most knowledge-based authentication methods rely on a cognitive function test, so mechanisms to assist users must be available. When authentication relies on performing an action on a separate device, it should be possible to complete the action without the need to transcribe information. It may not be possible to know what device-based authentication methods are available to a user; offering a choice of methods can allow them to choose the path that most suits them.

### Login forms

Websites can use username (or email) and password inputs as an authentication method if the author enables the user agent (browser) and any third-party password managers to fill in the fields. Generally, if the login form meets [Success Criterion 1.3.5 Input Purpose](identify-input-purpose), and the form controls have an appropriate accessible name in accordance with [Success Criterion 4.1.2 Name, Role, Value](name-role-value), user agents and password managers should be able to reliably recognize the fields and automatically fill them in.

A password entry field that prevents pasting, showing an error message advising the user to type the password manually.

However, if user agents and password managers are actively blocked from filling in the fields (for instance, by a script that prevents form fields from being automatically populated), or users are prevented from copy and paste operations (as they may rely on standalone/external third party password managers), then the page would fail this criterion unless an alternative is provided.

### Copy and paste

Copy and paste can be relied on to avoid transcription. Users can copy their login credentials from a local source (such as a standalone third-party password manager) and paste it into the username and password fields on a login form, or into a web-based command line interfaces asking for a password.

A time-based one-time password (TOTP) challenge that requires users to enter each digit of the 6-digit code into separate input fields – trying to paste the entire code only fills in one digit in the first input. A passnumber entry form, asking users to enter specific digits (the first, third, and fifth digit) of their passnumber.

Blocking people from pasting into authentication fields (see the example in login forms), or using a different format between the copied text and the input field (for example, "Enter the 1st, 3rd, and 5th character of your password"), would force the user to transcribe information and therefore fail this criterion, unless another method is available.

### Two-factor authentication systems (verification codes)

Beyond usernames and passwords, some sites may use two-factor authentication, asking the user to enter a verification code (also called a passcode or one-time password). A service that requires _manual_ transcription of a verification code is not compliant. As with usernames and passwords, it must be possible for a user to at least paste the code (such as from a standalone third-party password manager, text message application, or software-based security key), and to allow user agents to fill in the fields automatically.

There are scenarios where a verification code must be received or generated on a secondary device. For example, authenticating in a web browser on a laptop requires a verification code that is sent as an SMS text message to a mobile phone. However, in most cases, it is possible for the code to then be sent directly to the primary device, where it can then be copied and pasted (for example, by copying the code on the secondary device and emailing it to the primary device, or through the use of a shared cross-device clipboard where copying content on the secondary device makes it available to paste on the primary device). Evaluating whether or not the code can be seamlessly transferred from the secondary device to the primary device is _outside of the scope_ for this success criterion. For the purpose of evaluating web content that relies on authentication using these types of secondary device systems, it is assumed that provisions are in place that make the code available in the user's clipboard. Evaluating this criterion therefore only requires verification that the web content does allow pasting the clipboard content in the related authentication challenge field.

Note that two-factor systems that do not rely on codes – including hardware authentication devices (such as YubiKey), secondary applications (either on the same primary device, or on a secondary device) that expect the user to confirm that it is indeed them trying to log in, and authentication methods provided by the user's operating system (such as Windows Hello, or Touch ID/Face ID on macOS and iOS) – are _not_ a cognitive function test.

### Object Recognition

If a [CAPTCHA](https://www.w3.org/TR/turingtest/) is used as part of an authentication process, there must be a method that does not include a cognitive function test, unless it meets the exception. If the test is based on something the website has set such as remembering or transcribing a word, or recognizing a picture the website provided, that would be a cognitive functional test. While recognizing objects, or a picture the user has previously provided, are cognitive function tests, these are excepted in this criterion at AA level. However, these cases are _not_ excepted in [Success Criterion 3.3.9 Accessible Authentication (Enhanced)](accessible-authentication-enhanced) at AAA level.

An example of a CAPTCHA where users are asked to recognize common objects – in this case, selecting all images that contain a car.

An object in this context means the general English definition ("a material thing that can be seen and touched") and can include vehicles and animals. If the test goes beyond recognition (e.g. multiply the number cats by the number of dogs), that does not meet the exception.

Some forms of object recognition may require an understanding of a particular culture. For example, taxis can appear differently in different locales. This is an issue for many people, including people with disabilities, but it is not considered an accessibility-specific issue.

Some CAPTCHAs and cognitive function tests used for authentication may only appear in certain situations, such as when ad blockers are present, or after repeated incorrect password entry. This criterion applies when these tests are used regardless of whether they are used every time or only triggered by specific scenarios.

There are a number of technologies that can be employed to prevent scripted abuse of the authentication process.

  * [1.1.1. Rate-limited Access](https://www.ietf.org/archive/id/draft-private-access-tokens-01.html#name-rate-limited-access)
  * [1.1.2. Client Geo-Location](https://www.ietf.org/archive/id/draft-private-access-tokens-01.html#name-client-geo-location)
  * [1.1.3. Private Client Authentication](https://www.ietf.org/archive/id/draft-private-access-tokens-01.html#name-private-client-authenticati)



None of these systems are 100% effective. However, they may reduce the likelihood of a CAPTCHA being displayed.

### Personal Content

Personal content is sometimes used as a second factor for authentication. For example, as part of account creation the user would upload a picture, and when logging in they would be asked to select that picture from several possible alternatives. Care must be taken to provide adequate security in this case, since non-legitimate users might be able to guess the correct personal content when presented with a choice.

Text-based personal content does not qualify for this exception as it relies on recall (rather than recognition), and transcription (rather than selecting an item). Whilst picture-based personal content will still be a barrier for some people, text based versions tend to be a much larger barrier.

### Hiding characters

Another factor that can contribute to cognitive load is hiding characters when typing. Although this criterion requires that users do not have to type in (transcribe) a password, there are scenarios where that is necessary such as creating a password to be saved by a password manager. Providing a feature to optionally show a password can improve the chance of success for some people with cognitive disabilities or those who have difficulties with accurately typing.

A password field that hides the entered text by default, with a button next to it to toggle the display to show the password in clear text.

### Other types of cognitive tests

There are many variations of cognitive tests – from solving simple mathematical equations, to visual puzzles.

A mathematical CAPTCHA, asking the user to enter the result of an equation. A visual logic puzzle – the user is asked to rotate the 3D model of a walrus to match a given direction.

These types of cognitive tests are _not_ excepted. In order to satisfy the requirements of this criterion, an alternative authentication method that does not involve a cognitive test must be provided.

## Benefits of Accessible Authentication (Minimum)

People with cognitive issues relating to memory, reading (for example, dyslexia), numbers (for example, dyscalculia), or perception-processing limitations will be able to authenticate irrespective of the level of their cognitive abilities.

## Examples of Accessible Authentication (Minimum)

The examples of this success criterion are the same as the [Accessible Authentication (Enhanced)](accessible-authentication-enhanced.html#examples) examples.

  * A website uses a properly marked up username (or email) and password fields as the login authentication (meeting [Success Criterion 1.3.5 Input Purpose](identify-input-purpose) and [Success Criterion 4.1.2 Name, Role, Value](name-role-value)). The user's browser or integrated third-party password manager extension can identify the purpose of the inputs and automatically fill in the username and password.
  * A website does not block paste functionality. The user is able to use a third-party password manager to store credentials, copy them, and paste them directly into a login form.
  * A website uses WebAuthn so the user can authenticate with their device instead of username/password. The user's device could use any available modality. Common methods on laptops and phones are facial-scan, fingerprint, and PIN (Personal Identification Number). The website is not enforcing any particular use; it is assumed a user will set up a method that suits them.
  * A website offers the ability to login with a third-party provider using the OAuth method.
  * A website that requires two-factor authentication allows for multiple options for the 2nd factor, including a USB-based method where the user simply presses a button to enter a time-based token.
  * A website that requires two-factor authentication displays a QR code which can be scanned by an app on a user's device to confirm identity.
  * A website that requires two-factor authentication sends a notification to a user's device. The user must use their device's authentication mechanism (for example, user-defined PIN, fingerprint, facial recognition) to confirm identity.



## Resources

  * [Cognitive Accessibility Gap Analysis Topic 1: Authentication and Safety](https://www.w3.org/TR/coga-gap-analysis/#table1)
  * [Cognitive Accessibility Issue Papers 4. Web Security and Privacy Technologies](https://w3c.github.io/coga/issue-papers/#web-security-and-privacy-technologies) and [Web Security and Privacy Technologies](https://w3c.github.io/coga/issue-papers/privacy-security.html)
  * [Making Content Usable for People with Cognitive and Learning Disabilities 4.7.1 Provide a Login that Does Not Rely on Memory or Other Cognitive Skills](https://www.w3.org/TR/coga-usable/#provide-a-login-that-does-not-rely-on-memory-or-other-cognitive-skills-pattern)
  * [WebAuthN specification](https://www.w3.org/TR/webauthn/)
  * [WebAuthN Demo site](https://webauthn.io/)
  * [Web Authentication API on MDN](https://developer.mozilla.org/en-US/docs/Web/API/Web_Authentication_API)
  * [OAuth on Wikipedia](https://en.wikipedia.org/wiki/OAuth)
  * ["Let them paste passwords", from the UK's National Cyber Security Centre (archived)](https://webarchive.nationalarchives.gov.uk/ukgwa/20240306114738/https://www.ncsc.gov.uk/blog-post/let-them-paste-passwords)
  * [NIST SP 800-63 Digital Identity Guidelines (Second Public Draft of Revision 4) / SP 800-63B Authentication & Authenticator Management](https://pages.nist.gov/800-63-4/sp800-63b.html)

{% # Data for associated techniques is defined in understanding/understanding.11tydata.js %} {% include "understanding/techniques.html" %} 
  *[WYSIWYG]: What You See Is What You Get
  *[CAPTCHA]: Completely Automated Public Turing test to tell Computers and Humans Apart
