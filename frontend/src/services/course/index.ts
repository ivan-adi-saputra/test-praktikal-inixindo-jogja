import { BASE_URL } from "@/utils";
import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";

export const courseApi = createApi({
  reducerPath: "courseApi",
  baseQuery: fetchBaseQuery({ baseUrl: `${BASE_URL}/course` }),
  endpoints: (builder) => ({
    getCourse: builder.query<any, void>({
      query: () => `/`,
    }),
  }),
});

export const { useGetCourseQuery } = courseApi;
