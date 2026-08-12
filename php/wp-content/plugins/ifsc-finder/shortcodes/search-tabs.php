<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * [ifsc_search_tabs] — mirrors client/src/components/SearchTabs.js.
 */
add_shortcode('ifsc_search_tabs', function () {
    ob_start();
    ?>
    <div class="ifsc-search-tabs" id="ifsc-search-tabs">
        <div class="ifsc-tabs-nav">
            <button type="button" class="ifsc-tab-btn is-active" data-tab="bank">By Bank + City</button>
            <button type="button" class="ifsc-tab-btn" data-tab="branch">By Branch Name</button>
            <button type="button" class="ifsc-tab-btn" data-tab="ifsc">By IFSC Code</button>
        </div>

        <form class="ifsc-tab-panel is-active ifsc-form-row" data-panel="bank" id="ifsc-form-bank">
            <div class="ifsc-flex-1 ifsc-autosuggest" data-suggest-type="bank">
                <input type="text" class="ifsc-input" placeholder="Bank name e.g. State Bank of India" autocomplete="off">
                <ul class="ifsc-suggest-list" hidden></ul>
            </div>
            <div class="ifsc-flex-1 ifsc-autosuggest" data-suggest-type="city">
                <input type="text" class="ifsc-input" placeholder="City or town e.g. Mumbai" autocomplete="off">
                <ul class="ifsc-suggest-list" hidden></ul>
            </div>
            <button type="submit" class="ifsc-btn-primary" disabled>Search</button>
            <p class="ifsc-hint" hidden>Pick a bank from the suggestions to continue.</p>
        </form>

        <form class="ifsc-tab-panel ifsc-form-row" data-panel="branch" id="ifsc-form-branch" hidden>
            <div class="ifsc-flex-1 ifsc-autosuggest" data-suggest-type="branch">
                <input type="text" class="ifsc-input" placeholder="Branch name e.g. Fort Mumbai" autocomplete="off">
                <ul class="ifsc-suggest-list" hidden></ul>
            </div>
            <button type="submit" class="ifsc-btn-primary">Search</button>
        </form>
        <div id="ifsc-branch-results" class="ifsc-mt"></div>

        <form class="ifsc-tab-panel ifsc-form-row" data-panel="ifsc" id="ifsc-form-ifsc" hidden>
            <div class="ifsc-flex-1 ifsc-autosuggest" data-suggest-type="ifsc">
                <input type="text" class="ifsc-input" placeholder="Enter IFSC code e.g. SBIN0000001" autocomplete="off">
                <ul class="ifsc-suggest-list" hidden></ul>
            </div>
            <button type="submit" class="ifsc-btn-primary">Search</button>
        </form>
    </div>
    <?php
    return ob_get_clean();
});
