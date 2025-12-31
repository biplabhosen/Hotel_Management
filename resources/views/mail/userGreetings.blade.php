<!DOCTYPE html>

<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>শুভ নববর্ষ</title>
</head>
<body style="margin:0;padding:0;background-color:#0f172a;font-family:Arial, Helvetica, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" style="padding:40px 0;">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#111827;border-radius:10px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,0.4);">

```
            <!-- Header -->
            <tr>
                <td style="background:#1d4ed8;color:#ffffff;text-align:center;padding:30px;">
                    <h1 style="margin:0;font-size:28px;">
                        শুভ নববর্ষ  🎉
                    </h1>
                </td>
            </tr>

            <!-- Body -->
            <tr>
                <td style="padding:35px;color:#e5e7eb;">
                    <p style="font-size:16px;">
                        প্রিয় <strong>{{ $user->name }}</strong>,
                    </p>

                    <p style="font-size:15px;line-height:1.8;color:#d1d5db;">
                        নতুন বছর <strong> </strong> উপলক্ষে আপনাকে জানাই আন্তরিক শুভেচ্ছা ও অভিনন্দন।
                        আপনার বিশ্বাস ও সহযোগিতার জন্য আমরা কৃতজ্ঞ।
                    </p>

                    <p style="font-size:15px;line-height:1.8;color:#d1d5db;">
                        এই নতুন বছর আপনার জীবনে বয়ে আনুক সুস্বাস্থ্য, সাফল্য এবং সমৃদ্ধি।
                        আগামীতেও আমরা আপনাকে সর্বোচ্চ মানের সেবা প্রদান করতে প্রতিশ্রুতিবদ্ধ।
                    </p>

                    <!-- CTA Button -->
                    <div style="text-align:center;margin:35px 0;">
                        <a href="{{ $url ?? url('/') }}"
                           style="background:#2563eb;color:#ffffff;
                                  padding:14px 28px;
                                  text-decoration:none;
                                  border-radius:6px;
                                  font-size:15px;
                                  display:inline-block;">
                            ড্যাশবোর্ডে প্রবেশ করুন
                        </a>
                    </div>

                    <p style="margin-top:30px;font-size:15px;color:#e5e7eb;">
                        শুভেচ্ছান্তে,<br>
                        <strong>{{ config('app.name') }} টিম</strong>
                    </p>
                </td>
            </tr>

            <!-- Footer -->
            <tr>
                <td style="background:#020617;text-align:center;padding:18px;font-size:12px;color:#9ca3af;">
                    ©  {{ config('app.name') }}. সর্বস্বত্ব সংরক্ষিত।
                </td>
            </tr>

        </table>
    </td>
</tr>
```

</table>

</body>
</html>
