package com.gpsattendance.data

import retrofit2.http.*

interface ApiService {
    @POST("api/login")
    suspend fun login(@Body request: LoginRequest): LoginResponse

    @POST("api/verify-location")
    suspend fun verifyLocation(
        @Header("Authorization") token: String,
        @Body request: LocationRequest
    ): LocationResponse

    @POST("api/attendance/checkin")
    suspend fun checkIn(
        @Header("Authorization") token: String,
        @Body request: CheckInRequest
    ): CheckInResponse

    @POST("api/attendance/checkout")
    suspend fun checkOut(
        @Header("Authorization") token: String,
        @Body request: CheckOutRequest
    ): CheckOutResponse

    @GET("api/attendance/history")
    suspend fun getHistory(
        @Header("Authorization") token: String,
        @Query("emp_id") empId: Int
    ): HistoryResponse
}

data class LoginRequest(val username: String, val password: String)

data class LoginResponse(
    val token: String,
    val user: UserInfo
)

data class UserInfo(
    val id: Int,
    val username: String,
    val role: String,
    val employee_id: Int,
    val employee_name: String
)

data class LocationRequest(
    val employee_id: Int,
    val latitude: Double,
    val longitude: Double,
    val site_id: Int
)

data class LocationResponse(
    val valid: Boolean,
    val distance_meters: Double,
    val allowed_radius: Int,
    val site_name: String
)

data class CheckInRequest(
    val employee_id: Int,
    val site_id: Int,
    val latitude: Double,
    val longitude: Double
)

data class CheckInResponse(
    val success: Boolean,
    val attendance_id: Int,
    val check_in_time: String,
    val location_valid: Boolean,
    val distance_meters: Double
)

data class CheckOutRequest(
    val employee_id: Int,
    val attendance_id: Int,
    val latitude: Double,
    val longitude: Double
)

data class CheckOutResponse(
    val success: Boolean,
    val check_out_time: String,
    val location_valid: Boolean,
    val distance_meters: Double
)

data class HistoryResponse(
    val records: List<AttendanceHistoryRecord>
)

data class AttendanceHistoryRecord(
    val id: Int,
    val employee_id: Int,
    val site_id: Int,
    val site_name: String,
    val check_in_time: String,
    val check_out_time: String?,
    val check_in_valid: Boolean,
    val check_out_valid: Boolean?
)