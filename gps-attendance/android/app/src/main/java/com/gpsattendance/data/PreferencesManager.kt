package com.gpsattendance.data

import android.content.Context
import android.content.SharedPreferences
import androidx.security.crypto.EncryptedSharedPreferences
import androidx.security.crypto.MasterKey

class PreferencesManager(context: Context) {
    private val prefs: SharedPreferences = context.getSharedPreferences("gps_attendance", Context.MODE_PRIVATE)

    var authToken: String?
        get() = prefs.getString("auth_token", null)
        set(value) = prefs.edit().putString("auth_token", value).apply()

    var userId: Int
        get() = prefs.getInt("user_id", 0)
        set(value) = prefs.edit().putInt("user_id", value).apply()

    var employeeId: Int
        get() = prefs.getInt("employee_id", 0)
        set(value) = prefs.edit().putInt("employee_id", value).apply()

    var username: String?
        get() = prefs.getString("username", null)
        set(value) = prefs.edit().putString("username", value).apply()

    var isLoggedIn: Boolean
        get() = prefs.getBoolean("is_logged_in", false)
        set(value) = prefs.edit().putBoolean("is_logged_in", value).apply()

    fun logout() {
        prefs.edit().clear().apply()
    }
}