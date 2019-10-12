<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /** Comision por defecto de ganancia por servicio */
    const DEFAULT_PRODUCT_COMMISSION = 30;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'logo',
        'business_name',
        'email',
        'address',
        'phone',
        'login_schedule'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dates = ['deleted_at'];

    protected $appends = ['hasRole'];

    /**
     * Indica si un usuario es administrador
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Indica si el usuario es doctor
     *
     * @return bool
     */
    public function isDoctor()
    {
        return $this->hasRole('doctor');
    }

    /**
     * Indica si el usuario es secretaria
     *
     * @return bool
     */
    public function isSecretary()
    {
        return $this->hasRole('secretary');
    }

    /**
     * Indica si el usuario es asistente
     *
     * @return bool
     */
    public function isAssistant()
    {
        return $this->hasRole('assistant');
    }

    /**
     * Build has role property
     *
     * @return array
     */
    public function getHasRoleAttribute()
    {
        $hasRole = [];
        $roles = Role::all();
        foreach ($roles as $role) {
            $hasRole[$role->code] = false;
        }
        foreach ($this->roles as $role) {
            $hasRole[$role->code] = true;
        }

        return $hasRole;
    }

    /**
     * Indica si el usuario es Agente de ventas
     *
     * @return bool
     */
    public function isSellManager()
    {
        return $this->hasRole('sell_manager');
    }

    /**
     * Indica si un usuario posee un role
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        foreach ($this->roles as $r) {
            if ($r->code === $role) {
                return true;
            }
        }

        return false;
    }

    /**
     * Todos los servicios brindados por un Doctor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patientHistory()
    {
        return $this->hasMany(PatientHistory::class, 'doctor_id');
    }

    /**
     * Todos los servicios asistidos por este usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patientHistoryAssistant()
    {
        return $this->hasMany(PatientHistory::class, 'assistant_id');
    }

    /**
     * Todos los servicios que indican a este usuario como responsable
     * de los examenes de laboratorio
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patientHistoryResponsible()
    {
        return $this->hasMany(PatientHistory::class, 'responsible_id');
    }

    /**
     * Todos los servicios diagnosticados por un doctor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patientHistoryDiagnostic()
    {
        return $this->hasMany(PatientHistory::class, 'diagnostic_id');
    }

    /**
     * Todas las solicitudes de insumos generadas por el usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function supplyRequests()
    {
        return $this->hasMany(SupplyRequest::class, 'user_id');
    }

    /**
     * Todas las notas escritas por este usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'user_id');
    }

    /**
     * Todos los pagos registrados por este usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_created_id');
    }

    /**
     * Todas las preguntas hechas a este usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questionsTo()
    {
        return $this->hasMany(Question::class, 'to_id');
    }

    /**
     * Todas las preguntas formuladas por este usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questionsFrom()
    {
        return $this->hasMany(Question::class, 'from_id');
    }

    /**
     * Todas las comisiones de productos configuradas
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function commissionProducts()
    {
        return $this->belongsToMany(Product::class, 'product_user', 'user_id', 'product_id')
            ->withPivot(['commission']);
    }

    /**
     * Todas las citas registradas por este usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }

    /**
     * Todas las citas programadas para este doctor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function doctorAppointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    /**
     * Todas las cotizaciones generadas por este usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function budgets()
    {
        return $this->hasMany(Budget::class, 'user_id');
    }

    /**
     * Todas las llamadas que debe hacer un usuario secretaria
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function callLogs()
    {
        return $this->hasMany(CallLog::class, 'user_id');
    }

    /**
     * Notas de seguimiento
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trackingNotes()
    {
        return $this->hasMany(TrackingNote::class, 'user_id');
    }

    /**
     * Todos los horarios permitidos configurados para este usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function weekdays()
    {
        return $this->belongsToMany(Weekday::class, 'login_schedules', 'user_id', 'weekday_id')
            ->withPivot(['time_start', 'time_end'])
            ;
    }

    /**
     * Todos los movimientos de inventario que han sido
     * entregados a este usuario
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventoryMovements()
    {
        return $this->hasMany(User::class, 'user_id');
    }

    /**
     * Todos los gastos que han balanceado las comisiones de este doctor,
     * explicacion mas detallada en la clase Expense metodo doctorCommission()
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function doctorCommissionExpenses()
    {
        return $this->hasMany(Expense::class, 'doctor_commission_id');
    }

    /**
     * Pacientes registrados por un Agente de ventas
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sellManagerPatients()
    {
        return $this->hasMany(Patient::class, 'sell_manager_id');
    }

    /**
     * Roles
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id');
    }

    /**
     * Presupuestos enviados
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function callBudgets()
    {
        return $this->hasMany(CallBudget::class, 'sell_manager_id');
    }

    /**
     * Listado de agentes de venta
     *
     * @param Builder $query
     */
    public function scopeSellManagers(Builder $query)
    {
        $query->hasRole('sell_manager');

        if (! Auth::user()->isAdmin()) {
            $query->where('users.id', Auth::user()->id);
        }
    }

    /**
     * Filter by role
     *
     * @param Builder $query
     * @param string $role
     * @param string $boolean
     */
    public function scopeHasRole(Builder $query, $role, $boolean = 'and')
    {
        $roles = is_array($role) ? $role : [$role];

        $query
            ->select(['users.*'])
            ->join('user_role', 'user_id', '=', 'users.id')
            ->join('roles', 'role_id', '=', 'roles.id')
            ->distinct()
        ;

        foreach ($roles as $role) {
            $query->where('roles.code', '=', $role, $boolean);
        }
    }

    /**
     * Filter by role
     *
     * @param Builder $query
     * @param string $role
     * @param string $boolean
     */
    public function scopeHasNotRole(Builder $query, $role, $boolean = 'and')
    {
        $roles = is_array($role) ? $role : [$role];

        $query
            ->select(['users.*'])
            ->join('user_role', 'user_id', '=', 'users.id')
            ->join('roles', 'role_id', '=', 'roles.id')
            ->distinct()
        ;

        foreach ($roles as $role) {
            $query->where('roles.code', '<>', $role, $boolean);
        }
    }

    /**
     * Genera el id publico en base al nivel
     */
    public function generatePublicId()
    {
        $this->public_id = 'USR' . time();
    }

    /**
     * Indica si un usuario tiene permiso de acceder a una zona
     *
     * @param $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->isDoctor() && in_array($permission, [
                'product.create',
                'product.index',
                'patient.index',
                'patient.create',
                'budget.index',
                'budget.create',
                'service.search',
                'service.create',
                'supplyRequest.create',
                'question.index'
            ])) {
            return true;
        }

        if ($this->isAssistant() && in_array($permission, [
                'budget.index',
                'service.search',
                'supplyRequest.create',
                'question.index'
            ])) {
            return true;
        }

        if ($this->isSecretary() && in_array($permission, [
                'product.index',
                'patient.index',
                'budget.index',
                'service.search',
                'callLog.index',
                'supplyRequest.create',
                'payment.create',
                'expense.create',
                'expense.index',
                'supplier.create',
                'supplier.index',
                'appointment.create',
                'appointment.index',
                'tracking.index',
                'tracking.create'
            ])) {
            return true;
        }

        if ($this->isSellManager() && in_array($permission, [
                'callLog.index',
                'report.sellManagerPatients',
                'callBudgetSource.index',
                'callBudget.create',
                'callBudget.index'
            ])) {
            return true;
        }

        if ($this->management_inventory && in_array($permission, [
            'supply.inventoryType',
            'supply.inventoryBrand',
            'supply.inventoryIn',
            'supply.inventoryOut'
        ])) {
            return true;
        }

        if ($this->management_supply && in_array($permission, [
                'supply.create',
                'supply.index'
            ])) {
            return true;
        }

        return false;
    }
}
