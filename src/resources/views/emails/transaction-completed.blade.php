{{-- メールクライアント互換性（Gmail/Outlook等）を考慮し、スタイルはインラインで指定しています --}}
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>取引完了通知</title>
</head>

<body style="font-family: 'Helvetica Neue', Arial, 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', Meiryo, sans-serif; line-height: 1.6; color: #333333; background-color: #f4f4f4; margin: 0; padding: 0;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4; padding: 20px 0;">
        <tr>
            <td align="center">
                <table class="email-container" role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; background-color: #ffffff; border-radius: 8px;">
                    <tr>
                        <td class="email-header" style="background-color: #000000; color: #ffffff; padding: 30px 40px; text-align: center; border-radius: 8px 8px 0 0;">
                            <h1 style="margin: 0; font-size: 24px; font-weight: 700;">COACHTECH フリマ</h1>
                        </td>
                    </tr>

                    <tr>
                        <td class="email-body" style="padding: 40px;">
                            <h2 style="color: #000000; font-size: 20px; font-weight: 700; margin-top: 0; margin-bottom: 20px;">取引が完了しました</h2>

                            <p style="margin: 0 0 15px 0; font-size: 16px;">{{ $transaction->seller->name }} 様</p>

                            <p style="margin: 0 0 15px 0; font-size: 16px;">
                                {{ $transaction->buyer->name }} さんが商品の受け取りを完了しました。<br>
                                取引チャット画面から相手を評価してください。
                            </p>

                            <table class="item-info" role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f8f8f8; border-radius: 4px; margin: 20px 0;">
                                <tr>
                                    <td style="padding: 20px 20px 8px 20px; font-weight: 700; color: #666666; border-bottom: 1px solid #e0e0e0;">
                                        商品名
                                    </td>
                                    <td align="right" style="padding: 20px 20px 8px 20px; color: #000000; border-bottom: 1px solid #e0e0e0;">
                                        {{ $transaction->item->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 20px; font-weight: 700; color: #666666; border-bottom: 1px solid #e0e0e0;">
                                        価格
                                    </td>
                                    <td align="right" style="padding: 8px 20px; color: #000000; border-bottom: 1px solid #e0e0e0;">
                                        ¥{{ number_format($transaction->item->price) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 20px 20px 20px; font-weight: 700; color: #666666;">
                                        購入者
                                    </td>
                                    <td align="right" style="padding: 8px 20px 20px 20px; color: #000000;">
                                        {{ $transaction->buyer->name }}
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="padding: 30px 0;">
                                        <a href="{{ config('app.url') }}/transaction/{{ $transaction->id }}" class="button" style="display: inline-block; background-color: #FF5555; color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 5px; font-size: 16px; font-weight: 700;">
                                            取引チャット画面へ
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p class="note-text" style="font-size: 14px; color: #666666; margin: 30px 0 0 0;">
                                ※このメールは自動送信されています。<br>
                                ご不明な点がございましたら、COACHTECH フリマのお問い合わせページよりご連絡ください。
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td class="email-footer" style="background-color: #f8f8f8; padding: 20px 40px; text-align: center; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0; font-size: 14px; color: #666666;">&copy; 2026 COACHTECH フリマ. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>