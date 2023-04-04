<?php declare(strict_types = 1);
namespace noxkiwi\spotigame;

echo <<<HTML
<h1>Settings</h1>
<div class="alert alert-danger" role="alert">
    <h2>Danger Zone</h2>
    <p>
        A simple danger alert—check it out!
        <a href="/?context=settings&view=put&action=removeAccount">Delete Account</a>
    </p>
</div>
HTML;
