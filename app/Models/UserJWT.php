<?php

namespace App\Models;

use App\Helper\IRKHelp;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserJWT extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    public function __construct(Request $request)
	{
		// Call the parent constructor
		//parent::__construct();

		$slug = $request->route('slug');
		$x = $request->route('x');
		$this->base = 'v' . $x . '/' . $slug;

		$helper = new IRKHelp($request);
		$this->helper = $helper;

	}

    // protected $connection =  "pgsqlgcp_ess";
    
    // protected $table = "IDM_UserLogLogin";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nik',
        //'email',
        'pass',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        //'password',
        //'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            //'password' => 'hashed',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier(): string
    {
        return (string) $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getDataLogin(Request $request)
    {
       
        try {
            
            $response = $this->helper->Client('toverify_gcp')->request('POST', $this->base . '/credential/get', [
                'json' => [
                    'data' => ['code' => 1, 'body' => $request['data']]
                ]
            ]);

            $body = $response->getBody();

            $temp = json_decode($body);

            if($temp){
                if($temp->data == 'Login Berhasil' && !str_contains($temp->data,'menit')){
                    $user = new UserJWT($request);
                    $user->nik = $request['data']['nik'];
                    $user->pass = $request['data']['pass'];
                    unset($user['base']);
					unset($user['helper']);
                    return $user;
                }
            }

            return $temp;

        } catch (\Throwable $e) {
            return $e->getCode() == 0 ? 'Error Function Laravel = ' . $e->getMessage() : 'Error Database = ' . $e->getMessage();
        }
        
    }
}