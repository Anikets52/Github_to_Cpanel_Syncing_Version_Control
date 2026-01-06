<?php
date_default_timezone_set('Asia/Kolkata');
/**
 * cPanel-safe GitHub webhook deploy script
 * Yii2 compatible
 */

// ================= CONFIG =================
$secret    = 'eschoolplus12@#';   // must match GitHub webhook secret
$repoPath  = '/home/admin/public_html/uateschoolplus';
$php80     = '/opt/cpanel/ea-php80/root/usr/bin/php';
$composer  = '/home/admin/public_html/uateschoolplus/composer.phar_';
$branchRef = 'refs/heads/main';

// Log setup
$logDir  = __DIR__ . '/deploy_logs';
$logFile = $logDir . '/deploy.log';
// =========================================

// ---------- Permission-safe logging ----------
if (!is_dir($logDir)) {
    @mkdir($logDir, 0777, true);
    @chmod($logDir, 0777);
}
if (!file_exists($logFile)) {
    @touch($logFile);
    @chmod($logFile, 0777);
}

function logMsg($msg)
{
    global $logFile;
    @file_put_contents(
        $logFile,
        '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL,
        FILE_APPEND
    );
}

// ---------- Allow only POST ----------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// ---------- Read payload ----------
$payload = file_get_contents('php://input');

// ---------- Verify GitHub signature ----------
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$expected  = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if (!hash_equals($expected, $signature)) {
    logMsg('Invalid webhook signature');
    http_response_code(403);
    exit('Invalid signature');
}

// ---------- Decode payload ----------
$data = json_decode($payload, true);

// ---------- Check branch ----------
if (($data['ref'] ?? '') !== $branchRef) {
    logMsg('Ignored push to ' . ($data['ref'] ?? 'unknown'));
    exit('Ignored branch');
}

logMsg('=== DEPLOY STARTED ===');

// ---------- Commands ----------
$commands = [
    "cd $repoPath",
    "git pull origin main",
    //   "git -C $repoPath pull origin main",
    // "git -c safe.directory=$repoPath -C $repoPath pull origin main",

    // $repoPath = '/home/admin/repositories/uateschoolplus';
    // $webPath  = '/home/admin/public_html/uateschoolplus';

    //Terminal
    // chmod 711 /home/admin
    // chmod 755 /home/admin/repositories
    // chmod 755 /home/admin/repositories/uateschoolplus

    // "rsync -av --delete --exclude='.git' --exclude='.htaccess' --exclude='deploy.php' --exclude='deploy_logs/' --exclude='frontend/web/.htaccess' --exclude='backend/web/.htaccess' --exclude='common/config/*-local.php' $repoPath/ $webPath/",

];

foreach ($commands as $cmd) {
    logMsg("Running: $cmd");
    exec("$cmd 2>&1", $output);
    logMsg(implode("\n", $output));
}

logMsg('=== DEPLOY FINISHED ===');

echo 'DEPLOY OK';

/*

ssh-keygen -t rsa -b 4096
\OR\
ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa_cpanel

cd .ssh
cat id_rsa_cpanel.pub
//OR//
cat ~/.ssh/id_rsa_cpanel.pub

[SSH Access -> authorize]

ssh -T git@github.com
[Enter passphrase for key '/home/admin/.ssh/id_rsa':
Hi Anikets52/eschoolplus! You've successfully authenticated, but GitHub does not provide shell access.]

cd /home/admin/public_html/uateschoolplus
git clone git@github.com:Anikets52/eschoolplus.git .
 


//Installing composer for Vendor folder
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ls
[composer-setup.php]
php composer-setup.php
[composer.phar]
php composer.phar --version
cd ~/public_html/uateschoolplus
php composer.phar install

//to check php version
/opt/cpanel/ea-php80/root/usr/bin/php -v
/opt/cpanel/ea-php82/root/usr/bin/php composer.phar install


php version can be changes through this line in .htaccess:
<IfModule mime_module>
  AddHandler application/x-httpd-ea-php80___lsphp .php .php7 .phtml
</IfModule>


chmod -R 777 backend/runtime frontend/runtime console/runtime backend/web/assets frontend/web/assets

//if .git repository not found error
cd /home/admin/public_html/uateschoolplus
chown -R admin:admin .git
chmod -R 755 .git

//if fatal: detected dubious ownership in repository at '/home/admin/public_html/uateschoolplus'
git config --global --add safe.directory /home/admin/public_html/uateschoolplus
git config --system --add safe.directory /home/admin/public_html/uateschoolplus
git config --add safe.directory /home/admin/public_html/uateschoolplus


//for resync
chmod 711 /home/admin
chmod 755 /home/admin/repositories
chmod 755 /home/admin/repositories/uateschoolplus


Main Issue : error: cannot open '.git/FETCH_HEAD': Permission denied
On shared cPanel:
  - PHP (lsphp) runs as a restricted web user
  - That user is explicitly blocked from writing to .git/

👉 Conclusion
❌ You CANNOT run git pull from a PHP webhook on shared cPanel
✔ This is intentional hosting security
 
Options:
Option 1 — Manual deploy (official, supported)
 - Push to GitHub
 - Open cPanel → Git™ Version Control
 - Click Deploy HEAD Commit
   [This runs your .cpanel.yml]

Option 2 — Use Cron-based auto-deploy (100% reliable)
 - Step 1 — Create a deploy script (safe)
          - /home/admin/deploy.sh:
          - #!/bin/bash
            cd /home/admin/public_html/uateschoolplus || exit 1
            git pull origin main
            /opt/cpanel/ea-php82/root/usr/bin/php composer.phar_ install --no-dev --optimize-autoloader
            chmod -R 775 backend/runtime frontend/runtime console/runtime backend/web/assets frontend/web/assets
          - Make executable:
            chmod +x /home/admin/deploy.sh

 - Step 2 — Add Cron Job in cPanel [cPanel → Cron Jobs]
          - Add:
*/
             # [*/1 * * * * /home/admin/deploy.sh >> /home/admin/deploy.log 2>&1  ]
/*

*/