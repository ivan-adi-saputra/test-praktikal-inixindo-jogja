"use client";
import { store } from "@/services/store";
import { NextPage } from "next";
import { ReactNode } from "react";
import { Provider } from "react-redux";

interface Props {
  children: ReactNode;
}

const ReduxProvider: NextPage<Props> = ({ children }) => {
  return <Provider store={store}>{children}</Provider>;
};

export default ReduxProvider;
