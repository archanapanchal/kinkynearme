<?php

// set configuration items
config([
    'services.facebook.redirect'          => route('social.user.login.callback', [getSocialProviderKey('facebook')]),
    'services.google.redirect'          => route('social.user.login.callback', [getSocialProviderKey('google')]),
]);
