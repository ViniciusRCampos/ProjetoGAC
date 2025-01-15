<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'balance',
        'account_number',
        'active',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Function to create a unique random account number;
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($account) {
            do {
                $number = str_pad(mt_rand(1, 99999999), 10, '0', STR_PAD_LEFT);
            } while (self::where('account_number', $number)->exists());

            $account->accountNumber = $number;
        });
    }
    /**
     * function to create a new account and set balance value
     * @param int $userId
     * @param int $balance
     * @return \App\Models\Account|\Illuminate\Database\Eloquent\Model
     */
    public static function createNewAccount(int $userId, int $balance = 0): Account|Model
    {
        return self::create([
            'user_id' => $userId,
            'balance' => $balance,
        ]);
    }
    /**
     * A function to find an account by account number or user id
     * @param array $data
     * @throws \InvalidArgumentException
     * @throws \ErrorException
     * @return \App\Models\Account|\Illuminate\Database\Eloquent\Model
     */
    public static function findAccountId(array $data): Account|Model
    {
        if (empty($data['account_number']) && empty($data['user_id'])) {
            throw new \InvalidArgumentException('You must provide either account_number or user_id.');
        }
        if (isset($data['account_number'])) {
            return self::where('account_number', $data['account_number'])->firstOrFail();
        }
        if (isset($data['user_id'])) {
            return self::where('user_id', $data['user_id'])->firstOrFail();
        }
        throw new \ErrorException('Account not found with provided criteria');
    }
    /**
     * Function to update account balance
     * @param array $data
     * @param int $balanceoperationLog
     * @return \App\Models\Account|\Illuminate\Database\Eloquent\Model
     */
    public static function updateBalance(array $data, int $balance): Account|Model
    {
        $account = self::findAccountId($data);
        $account->increment('balance', $balance);
        return $account;
    }

    /**
     * Function to deactivate an account
     * @param int $accountId
     * @return \App\Models\Account|\Illuminate\Database\Eloquent\Model
     */
    public static function deactivateAccount(int $accountId): Account|Model
    {
        $account = self::findById($accountId);
        $account->update(['active' => false]);
        return $account;
    }
}
