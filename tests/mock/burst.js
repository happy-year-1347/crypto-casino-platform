// Fires N identical requests at the same instant (Promise.all) and prints the
// status codes, so the PowerShell test can check how many were accepted.
//   node burst.js <n> <method> <url> <bodyFile> [bearerToken] [form]
// The body comes from a file so quoting cannot mangle it.
import fs from 'node:fs';
const n = parseInt(process.argv[2], 10);
const method = process.argv[3];
const url = process.argv[4];
const bodyFile = process.argv[5] || '';
const body = bodyFile && fs.existsSync(bodyFile) ? fs.readFileSync(bodyFile, 'utf8').replace(/^﻿/, '').trim() : '';
const token = process.argv[6] || '';
const form = process.argv[7] === 'form';

(async () => {
    const headers = { Accept: 'application/json' };
    if (token) headers.Authorization = 'Bearer ' + token;
    headers['Content-Type'] = form ? 'application/x-www-form-urlencoded' : 'application/json';

    // build every request first, then release them together
    const calls = Array.from({ length: n }, () => () => fetch(url, { method, headers, body }).then(r => r.status).catch(() => 0));
    const results = await Promise.all(calls.map(c => c()));
    console.log(results.join(','));
})();
