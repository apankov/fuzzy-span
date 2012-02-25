Fuzzy Span replacement
======================

This is very simple module that replaces standard Kohana's Date::fuzzy_span method.
New implementation is just a port of
[distance_of_time_in_words()](https://github.com/rails/rails/blob/92088131ac15734f2227e9d85ea751e3f89b0116/actionpack/lib/action_view/helpers/date_helper.rb#L67)
helper from Ruby on Rails.

Usage
-----

Use this module if you are not completely satisfied by Kohana's way of generating fuzzy time distances.
You can just add this module to Kohana::modules in your bootstrap.php, that's it.

Credits
-------

  * Ruby on Rails, https://github.com/rails thank you for all
  * Kohana, https://github.com/kohana
  * Sinan Eldem, 3.2 Integration http://www.sinaneldem.com.tr