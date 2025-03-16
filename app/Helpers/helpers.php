<?php
function session_notify($variant, $message, $title = '' ) {
    request()->session()->flash('variant', $variant);
    request()->session()->flash('title', $title);
    request()->session()->flash('message', __($message));
}
?>
