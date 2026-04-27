import type { ReactNode } from "react";

type Props = {
  title: string;
  children: ReactNode;
  footer?: ReactNode;
};

export default function AuthLayout({ title, children, footer }: Props) {
  return (
    <div className="auth-shell auth-mobile-shell">
      <div className="auth-mobile-screen">
        <div className="auth-hero">
          <div className="auth-logo-badge">
            <div className="auth-logo-circle">⛨</div>
          </div>
          <div className="auth-title">{title}</div>
        </div>

        <div className="auth-form-panel">
          {children}
          {footer ? <div className="auth-footer">{footer}</div> : null}
        </div>
      </div>
    </div>
  );
}
