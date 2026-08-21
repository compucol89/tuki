# 02-route-surface.md — Organizer Routes Inventory

**Date:** 2026-08-21
**Total routes:** 98 (across 5 sub-files + master)

---

## Classification

### DIRECT — Loads dashboard page
| Method | URI | Name | Controller | Middleware |
|--------|-----|------|------------|-----------|
| GET | `/organizer/dashboard` | `organizer.dashboard` | `OrganizerController@index` | auth, locale, Deactive, EmailStatus |

### SHARED — Same layout, different content
| # | Method | URI | Name | Controller |
|---|--------|-----|------|------------|
| 1 | GET | `/organizer/edit-profile` | `organizer.edit.profile` | `OrganizerController@edit_profile` |
| 2 | POST | `/organizer/organizer-update-profile` | `organizer.update_profile` | `OrganizerController@update_profile` |
| 3 | GET | `/organizer/change-password` | `organizer.change.password` | `OrganizerController@change_password` |
| 4 | POST | `/organizer/update-password` | `organizer.update_password` | `OrganizerController@updated_password` |
| 5 | GET | `/organizer/verify/email` | `organizer.verify.email` | `OrganizerController@verify_email` |
| 6 | POST | `/organizer/send-verify/link` | `organizer.send.verify.link` | `OrganizerController@send_link` |
| 7 | GET | `/organizer/transaction` | `organizer.transcation` | `OrganizerController@transaction` |
| 8 | GET | `/organizer/monthly-income` | `organizer.monthly_income` | `OrganizerController@monthly_income` |
| 9 | GET | `/organizer/withdraw` | `organizer.withdraw` | `OrganizerWithdrawController@index` |
| 10 | GET | `/organizer/withdraw/create` | `organizer.withdraw.create` | `OrganizerWithdrawController@create` |
| 11 | POST | `/organizer/withdraw/send-request` | `organizer.withdraw.send-request` | `OrganizerWithdrawController@send_request` |
| 12 | GET | `/organizer/support-tikcet/create` | `organizer.support_ticket.create` | `SupportTicketController@create` |
| 13 | GET | `/organizer/support-tikcet/tickets` | `organizer.support_tickets` | `SupportTicketController@index` |
| 14 | GET | `/organizer/support-tikcet/message/{id}` | `organizer.support_tickets.message` | `SupportTicketController@message` |
| 15 | GET | `/organizer/event-management/events/` | `organizer.event_management.event` | `EventController@index` |
| 16 | GET | `/organizer/choose-event-type/` | `choose-event-type` | `EventController@choose_event_type` |
| 17 | GET | `/organizer/add-event/` | `organizer.add.event.event` | `EventController@add_event` |
| 18 | GET | `/organizer/edit-event/{id}` | `organizer.event_management.edit_event` | `EventController@edit` |
| 19 | GET | `/organizer/event/ticket` | `organizer.event.ticket` | `TicketController@index` |
| 20 | GET | `/organizer/event-booking` | `organizer.event.booking` | `EventBookingController@index` |
| 21 | GET | `/organizer/event-booking/evento/{eventId}` | `organizer.event_booking.byEvent` | `EventBookingController@byEvent` |
| 22 | GET | `/organizer/event-booking/details/{id}` | `organizer.event_booking.details` | `EventBookingController@show` |
| 23 | GET | `/organizer/event-booking/report` | `organizer.event_booking.report` | `EventBookingController@report` |
| 24 | GET | `/organizer/telegram-bot` | `organizer.telegram_bot.index` | `TelegramBotController@index` |
| 25 | GET | `/organizers/email/verify` | — | `OrganizerController@confirm_email` |

### AUTH — Guest-only routes
| # | Method | URI | Name | Controller |
|---|--------|-----|------|------------|
| 1 | GET | `/organizer/login` | `organizer.login` | `OrganizerController@login` |
| 2 | GET | `/organizer/signup` | `organizer.signup` | `OrganizerController@signup` |
| 3 | POST | `/organizer/create` | `organizer.create` | `OrganizerController@create` |
| 4 | POST | `/organizer/store` | `organizer.authentication` | `OrganizerController@authentication` |
| 5 | GET | `/organizer/forget-password` | `organizer.forget.password` | `OrganizerController@forget_passord` |
| 6 | POST | `/organizer/send-forget-mail` | `organizer.forget.mail` | `OrganizerController@forget_mail` |
| 7 | GET | `/organizer/reset-password` | `organizer.reset.password` | `OrganizerController@reset_password` |
| 8 | POST | `/organizer/update-forget-password` | `organizer.update-forget-password` | `OrganizerController@update_password` |
| 9 | GET | `/organizador/login` | `organizador.login` | (Spanish alias) |
| 10 | GET | `/organizador/registro` | `organizador.registro` | (Spanish alias) |
| 11 | GET | `/organizador/olvide-contrasena` | `organizador.olvide-contrasena` | (Spanish alias) |
| 12 | GET | `/organizador/restablecer-contrasena` | `organizador.restablecer-contrasena` | (Spanish alias) |

### API/AJAX — No view rendering
| # | Method | URI | Name | Controller |
|---|--------|-----|------|------------|
| 1 | POST | `/organizer/transcation/delete` | `organizer.transcation.delete` | `OrganizerController@destroy` |
| 2 | POST | `/organizer/transcation/bulk-delete` | `organizer.transcation.bulk_delete` | `OrganizerController@bulk_destroy` |
| 3 | POST | `/organizer/change-theme` | `organizer.change_theme` | `OrganizerController@changeTheme` |
| 4 | POST | `/organizer/event/{id}/update-status` | `organizer.event_management.event.event_status` | `EventController@updateStatus` |
| 5 | POST | `/organizer/event/{id}/update-featured` | `organizer.event_management.event.update_featured` | `EventController@updateFeatured` |
| 6 | POST | `/organizer/delete-event/{id}` | `organizer.event_management.delete_event` | `EventController@destroy` |
| 7 | POST | `/organizer/event-update` | `organizer.event.update` | `EventController@update` |
| 8 | POST | `/organizer/event-store` | `organizer.event_management.store_event` | `EventController@store` |
| 9 | POST | `/organizer/event-imagesstore` | `organizer.event.imagesstore` | `EventController@gallerystore` |
| 10 | POST | `/organizer/event-imagermv` | `organizer.event.imagermv` | `EventController@imagermv` |
| 11 | POST | `/organizer/event-img-dbrmv` | `organizer.event.imgdbrmv` | `OrganizerController@imagedbrmv` |
| 12 | POST | `/organizer/delete-date/{id}` | `organizer.event.delete.date` | `EventController@deleteDate` |
| 13 | POST | `/organizer/update-ticket-setting` | `organizer.event_management.update_ticket_setting` | `EventController@updateTicketSetting` |
| 14 | POST | `/organizer/event/ticket/store-ticket` | `organizer.ticket_management.store_ticket` | `TicketController@store` |
| 15 | POST | `/organizer/event/ticket/delete-ticket` | `organizer.ticket_management.delete_ticket` | `TicketController@destroy` |
| 16 | POST | `/organizer/delete-variation/{id}` | `organizer.delete.variation` | `TicketController@delete_variation` |
| 17 | POST | `/organizer/ticket_management/update/ticket` | `organizer.ticket_management.update_ticket` | `TicketController@update` |
| 18 | POST | `/organizer/bulk/delete/event` | `organizer.event_management.bulk_delete_event` | `EventController@bulk_delete` |
| 19 | POST | `/organizer/bulk/delete/bulk/event/ticket` | `organizer.event_management.bulk_delete_event_ticket` | `TicketController@bulk_delete` |
| 20 | POST | `/organizer/event-booking/update/payment-status/{id}` | `organizer.event_booking.update_payment_status` | `EventBookingController@updatePaymentStatus` |
| 21 | POST | `/organizer/{id}/delete` | `organizer.event_booking.delete` | `EventBookingController@destroy` |
| 22 | POST | `/organizer/event-booking/bulk-delete` | `organizer.event_booking.bulk_delete` | `EventBookingController@bulkDestroy` |
| 23 | GET | `/organizer/event-booking/export` | `organizer.event_bookings.export` | `EventBookingController@export` |
| 24 | POST | `/organizer/withdraw/witdraw/bulk-delete` | `organizer.witdraw.bulk_delete_withdraw` | `OrganizerWithdrawController@bulkDelete` |
| 25 | POST | `/organizer/withdraw/witdraw/delete` | `organizer.witdraw.delete_withdraw` | `OrganizerWithdrawController@Delete` |
| 26 | GET | `/organizer/get-withdraw-method/input/{id}` | — | `OrganizerWithdrawController@get_inputs` |
| 27 | GET | `/organizer/withdraw/balance-calculation/{method}/{amount}` | — | `OrganizerWithdrawController@balance_calculation` |
| 28 | POST | `/organizer/support-tikcet/store` | `organizer.support_ticket.store` | `SupportTicketController@store` |
| 29 | POST | `/organizer/support-tikcet/zip-upload` | `organizer.support_ticket.zip_file.upload` | `SupportTicketController@zip_file_upload` |
| 30 | POST | `/organizer/support-tikcet/reply/{id}` | `organizer.support_ticket.reply` | `SupportTicketController@ticketreply` |
| 31 | POST | `/organizer/support-tikcet/delete/{id}` | `organizer.support_tickets.delete` | `SupportTicketController@delete` |
| 32 | POST | `/organizer/support-tikcet/bulk/delete/` | `organizer.support_tickets.bulk_delete` | `SupportTicketController@bulk_delete` |
| 33 | POST | `/organizer/telegram-bot/link-token` | `organizer.telegram_bot.generate_token` | `TelegramBotController@generate` |
| 34 | POST | `/organizer/event/ticket/free-limit` | `organizer.event.ticket.free_limit` | `TicketController@updateFreeTicketLimit` |
| 35 | POST | `/organizer/event/{event}/ai-assistant/*` | Various | `EventAiAssistantController` |
| 36 | GET | `/organizer/pwa/` | `organizer.pwa` | `OrganizerController@pwa` |
| 37 | POST | `/organizer/check-qrcode/` | `check-qrcode` | `OrganizerController@check_qrcode` |

### URL Aliases (Spanish)
| English | Spanish |
|---------|---------|
| `/organizer/login` | `/organizador/login` |
| `/organizer/signup` | `/organizador/registro` |
| `/organizer/forget-password` | `/organizador/olvide-contrasena` |
| `/organizer/reset-password` | `/organizador/restablecer-contrasena` |

---

## Notes
- `support-tikcet` is a typo in the route URI (should be `support-ticket`). Documented but not fixed in this wave.
- `transcation` is a typo (should be `transaction`). Persistent across codebase.
- `witdraw` is a typo (should be `withdraw`). Persistent.
