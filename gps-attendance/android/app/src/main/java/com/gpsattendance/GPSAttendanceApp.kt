package com.gpsattendance

import android.app.Application
import android.content.Context
import androidx.room.Room
import com.gpsattendance.data.AppDatabase
import com.gpsattendance.data.PreferencesManager

class GPSAttendanceApp : Application() {
    lateinit var db: AppDatabase
        private set
    lateinit var prefs: PreferencesManager
        private set

    override fun onCreate() {
        super.onCreate()
        instance = this
        db = Room.databaseBuilder(this, AppDatabase::class.java, "gps_attendance.db").build()
        prefs = PreferencesManager(this)
    }

    companion object {
        lateinit var instance: GPSAttendanceApp
            private set
    }
}