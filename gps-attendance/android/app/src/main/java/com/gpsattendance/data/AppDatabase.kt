package com.gpsattendance.data

import androidx.room.*

@Entity(tableName = "pending_attendance")
data class PendingAttendance(
    @PrimaryKey(autoGenerate = true) val id: Long = 0,
    val employeeId: Int,
    val siteId: Int,
    val type: String,
    val latitude: Double,
    val longitude: Double,
    val timestamp: Long = System.currentTimeMillis()
)

@Entity(tableName = "attendance_history")
data class AttendanceRecord(
    @PrimaryKey(autoGenerate = true) val id: Long = 0,
    val serverId: Int?,
    val employeeId: Int,
    val siteId: Int,
    val siteName: String,
    val checkInTime: String,
    val checkOutTime: String?,
    val checkInValid: Boolean,
    val checkOutValid: Boolean?
)

@Dao
interface PendingAttendanceDao {
    @Query("SELECT * FROM pending_attendance ORDER BY timestamp ASC")
    suspend fun getAll(): List<PendingAttendance>

    @Insert
    suspend fun insert(attendance: PendingAttendance)

    @Delete
    suspend fun delete(attendance: PendingAttendance)

    @Query("DELETE FROM pending_attendance")
    suspend fun deleteAll()
}

@Dao
interface AttendanceRecordDao {
    @Query("SELECT * FROM attendance_history WHERE employeeId = :empId ORDER BY checkInTime DESC LIMIT 30")
    suspend fun getByEmployee(empId: Int): List<AttendanceRecord>

    @Insert
    suspend fun insertAll(records: List<AttendanceRecord>)

    @Query("DELETE FROM attendance_history")
    suspend fun deleteAll()
}

@Database(entities = [PendingAttendance::class, AttendanceRecord::class], version = 1)
abstract class AppDatabase : RoomDatabase() {
    abstract fun pendingAttendanceDao(): PendingAttendanceDao
    abstract fun attendanceRecordDao(): AttendanceRecordDao
}