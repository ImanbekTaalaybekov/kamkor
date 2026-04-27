type Props = {
  label?: string;
  value: string;
  onChange: (value: string) => void;
  placeholder?: string;
  type?: string;
  error?: boolean;
};

export default function Input({
  label,
  value,
  onChange,
  placeholder = "",
  type = "text",
  error = false,
}: Props) {
  return (
    <label className="field">
      {label ? <span className="label">{label}</span> : null}
      <input
        className={error ? "input input-error" : "input"}
        type={type}
        value={value}
        onChange={(e) => onChange(e.target.value)}
        placeholder={placeholder}
      />
    </label>
  );
}
