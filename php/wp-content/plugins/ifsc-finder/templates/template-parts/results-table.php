<?php
/**
 * Usage: ifsc_finder_results_table($results);
 * Mirrors client/src/components/ResultsTable.js.
 */
if (!defined('ABSPATH')) {
    exit;
}
require_once IFSC_FINDER_DIR . 'templates/template-parts/flag-badge.php';

function ifsc_finder_results_table($results)
{
    if (empty($results)) {
        return;
    }
    ?>
    <div class="ifsc-table-wrap">
        <table class="ifsc-results-table">
            <thead>
                <tr>
                    <th scope="col">IFSC Code</th>
                    <th scope="col">Bank</th>
                    <th scope="col">Branch</th>
                    <th scope="col">City, State</th>
                    <th scope="col">Transaction Modes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $branch) : ?>
                    <tr>
                        <td>
                            <a class="ifsc-code-link" href="<?php echo esc_url(home_url('/ifsc/' . $branch['ifsc'])); ?>">
                                <?php echo esc_html($branch['ifsc']); ?>
                            </a>
                        </td>
                        <td><?php echo esc_html($branch['bank'] ?: $branch['bankcode']); ?></td>
                        <td><?php echo esc_html($branch['branch']); ?></td>
                        <td class="ifsc-muted"><?php echo esc_html($branch['city'] . ', ' . $branch['state']); ?></td>
                        <td>
                            <div class="ifsc-badge-row">
                                <?php
                                ifsc_finder_flag_badge('NEFT', (bool) $branch['neft']);
                                ifsc_finder_flag_badge('RTGS', (bool) $branch['rtgs']);
                                ifsc_finder_flag_badge('IMPS', (bool) $branch['imps']);
                                ifsc_finder_flag_badge('UPI', (bool) $branch['upi']);
                                ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
