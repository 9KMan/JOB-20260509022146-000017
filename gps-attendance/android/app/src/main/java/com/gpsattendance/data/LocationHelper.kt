package com.gpsattendance.data

import android.content.Context
import android.os.Looper
import com.google.android.gms.location.*
import kotlinx.coroutines.suspendCancellableCoroutine
import kotlin.coroutines.resume

class LocationHelper(private val context: Context) {
    private val fusedLocationClient: FusedLocationProviderClient =
        LocationServices.getFusedLocationProviderClient(context)

    private val locationRequest = LocationRequest.Builder(
        Priority.PRIORITY_HIGH_ACCURACY,
        5000
    ).apply {
        setMinUpdateIntervalMillis(2000)
        setWaitForAccurateLocation(true)
    }.build()

    suspend fun getCurrentLocation(): LocationData? = suspendCancellableCoroutine { cont ->
        try {
            val callback = object : LocationCallback() {
                override fun onLocationResult(result: LocationResult) {
                    fusedLocationClient.removeLocationUpdates(this)
                    val location = result.lastLocation
                    if (location != null) {
                        cont.resume(LocationData(location.latitude, location.longitude))
                    } else {
                        cont.resume(null)
                    }
                }
            }
            fusedLocationClient.requestLocationUpdates(locationRequest, callback, Looper.getMainLooper())
            cont.invokeOnCancellation {
                fusedLocationClient.removeLocationUpdates(callback)
            }
        } catch (e: SecurityException) {
            cont.resume(null)
        }
    }

    data class LocationData(val latitude: Double, val longitude: Double)
}