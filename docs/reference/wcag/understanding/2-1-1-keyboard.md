> SOURCE OF TRUTH - TukiPass. No editar.
> Fuente publica: https://raw.githubusercontent.com/w3c/wcag/main/understanding/20/keyboard.html
> Espejo oficial: w3c/wcag (GitHub)
> Capturado: 2026-08-21

# Understanding Keyboard

## In brief

Goal
    Everything can be done with a keyboard except freehand movements.
What to do
    Ensure pointer actions have a keyboard equivalent.
Why it's important
    Many people rely on the keyboard interface, including blind and some mobility impaired people.

## Intent of Keyboard

The intent of this success criterion is to ensure that, wherever possible, content can be operated through a keyboard or keyboard interface (so an alternate keyboard can be used). When content can be operated through a keyboard or alternate keyboard, it is operable by people with no vision (who cannot use devices such as mice that require eye-hand coordination) as well as by people who must use alternate keyboards or input devices that act as keyboard emulators. Keyboard emulators include speech input software, sip-and-puff software, on-screen keyboards, scanning software and a variety of assistive technologies and alternate keyboards. Individuals with low vision also may have trouble tracking a pointer and find the use of software much easier (or only possible) if they can control it from the keyboard.

Examples of "specific timings for individual keystrokes" include situations where a user would be required to repeat or execute multiple keystrokes within a short period of time or where a key must be held down for an extended period before the keystroke is registered.

The phrase "except where the underlying function requires input that depends on the path of the user's movement and not just the endpoints" is included to separate those things that cannot reasonably be controlled from a keyboard.

Most actions carried out by a pointing device can also be done from the keyboard (for example, clicking, selecting, moving, sizing). However, there is a small class of input that is done with a pointing device that cannot be done from the keyboard in any known fashion without requiring an inordinate number of keystrokes. Free hand drawing, or watercolor painting require path dependent input. Drawing straight lines, regular geometric shapes, re-sizing windows and dragging objects to a location (when the path to that location is not relevant) do not require path dependent input.

The use of MouseKeys would not satisfy this success criterion because it is not a keyboard equivalent to the application; it is a mouse equivalent (i.e., it looks like a mouse to the application).

It is assumed that the design of user input features takes into account that operating system keyboard accessibility features may be in use. For example, modifier key locking may be turned on. Content continues to function in such an environment, not sending events that would collide with the modifier key lock to produce unexpected results.

Platforms and user agents usually have conventions for how web content or applications are controlled with a keyboard interface. If content does not follow the platform/user agent conventions it may be difficult to use, as users will need to learn different interaction methods. As a _best practice_ , content should follow the platform/user agent conventions. However, deviating from these conventions does _not_ fail the normative requirement of this success criterion.

For instance, buttons that have focus can generally be activated using both the `Enter` key and the `Space` bar. If a custom button control in a web application instead only reacts to `Enter` (or even a completely custom key or key combination), this still **satisfies** the requirements of this success criterion.

This success criterion does not require that every visible control that can be activated using a mouse or touchscreen must also be focusable and actionable using the keyboard. The normative requirement is only that there must be a way for keyboard interface users to perform the same, or comparable, actions and to operate the content. Generally, the easiest way to achieve this is to provide controls that can be operated with all possible input devices; however, if a web application implements a separate mode of operation for keyboard interface users, it will **not** fail the success criterion. However, authors are advised to consider how users will discover any keyboard equivalents which are available.

## Benefits of Keyboard

  * People who are blind (who cannot use devices such as mice that require eye-hand coordination)
  * People with low vision (who may have trouble finding or tracking a pointer indicator on screen)
  * Some people with hand tremors find using a mouse very difficult and therefore usually use a keyboard



## Examples of Keyboard

Example 1: A drawing Program
    A drawing program allows users to create, size, position and rotate objects from the keyboard.
Example 2: A drag and Drop Feature
    An application that uses drag and drop also supports "cut" and "paste" or form controls to move objects.
Example 3: Moving between and connecting discrete points
    A connect-the-dots program allows the user to move between dots on a screen and use the spacebar to connect the current dot to the previous one.
Example 4: Exception - Painting Program
    A watercolor painting program passes as an exception because the brush strokes vary depending on the speed and duration of the movements.
Example 5: Exception - Model helicopter flight training simulator
    A model helicopter flight training simulator passes as an exception because the nature of the simulator is to teach real-time behavior of a model helicopter.
Example 6: A PDA with an optional keyboard
    A PDA device that is usually operated via a stylus has an optional keyboard that can be attached. The keyboard allows full web browsing in standard fashion. The web content is operable because it was designed to work with keyboard-only access.
Example 7: Simple search form with pointer-operable submit button
    A search form includes a text input field followed by a submit button. The submit button itself has been coded so that it does _not_ receive focus, and can only be activated using a pointer input. However, since keyboard users can submit the search by pressing `Enter` in the text input after typing their search terms, the form passes this success criterion.

## Resources for Keyboard

{% # Data for associated techniques is defined in understanding/understanding.11tydata.js %} {% include "understanding/techniques.html" %} 
